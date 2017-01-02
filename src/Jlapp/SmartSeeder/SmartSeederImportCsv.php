<?php namespace Jlapp\SmartSeeder;

use Jlapp\SmartSeeder\SmartSeeder;
use Maatwebsite\Excel\Facades\Excel;

class SmartSeederImportCsv extends SmartSeeder
{
    protected $seed;
    protected $unique = [];
    protected $columns = [];
    protected $lookup = [];
    protected $model;
    protected $header = true;

    private $counters = [
        'new' => 0,
        'existing' => 0
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($params = null)
    {
        echo "\n\n" . '==== Seeding: ' . get_class($this);

        // fetch roles & permissions from CSV seed
        Excel::load($this->seed, function($reader)
        {
            $results = $this->parseDataFromReader($reader);

            foreach($results['data'] as $rowKey => $row)
            {
                $this->seedOneRecord($row);
            }
        });

        $this->displayStats();
    }

    private function parseDataFromReader($reader)
    {
        $reader->noHeading();
        $results = $reader->get();
        $data = $results->toArray();
        $headerRow = null;
        if ($this->header)
        {
            $headerRow = array_shift($data); // extract header row
        }

        $columnNames = $this->getColumnNames();

        return [
            'data' => $this->convertToAssociativeArray($columnNames, $data),
            'headerRow' => $headerRow
        ];
    }

    private function getColumnNames()
    {
        $names = [];
        foreach($this->columns as $key => $column)
        {
            if (is_array($column))
            {
                $names[] = $key;
            } else
            {
                $names[] = $column;
            }
        }
        return $names;
    }

    private function convertToAssociativeArray($keys, $data)
    {
        $assoc = [];
        foreach($data as $row)
        {
            $assoc[] = array_combine($keys, $row);
        }
        return $assoc;
    }

    private function seedOneRecord($row)
    {
        $this->rowData = $row;

        $this->lookupForeignKeys();

        $uniqueData = $this->formatUniqueRowData();

        $model = new $this->model;

        // create record if doesn't exist (based on unique columns only)
        $record = $model::firstOrNew($uniqueData);
        $this->updateCounters($record);
        $record->save();

        // update additional field
        $nonUniqueData = $this->formatNonUniqueRowData();
        $record->update($nonUniqueData);

        $this->insertCallback($record, $this->rowData);
    }

    private function lookupForeignKeys()
    {
        // foreach lookup column, fetch the foreign key
        foreach($this->columns as $key => $column)
        {
            if (
                is_array($column)
                && isset($column['lookup_column'])
                && isset($column['class_name'])
                && isset($column['foreign_key_column'])
            ) {
                $columnName = $key;
                $model = new $column['class_name'];

                // add foreign key id to row data
                $fkId = $model::where($column['lookup_column'], $this->rowData[$columnName])->take(1)->pluck('id');
                $this->rowData{$column['foreign_key_column']} = $fkId;

                // remove text lookup column
                unset($this->rowData[$columnName]);

                if (count($this->unique) > 0) {
                    // swap unique column name for foreign key column name
                    if (!in_array($column['foreign_key_column'], $this->unique)) {
                        $this->unique[] = $column['foreign_key_column'];
                    }
                    if (in_array($columnName, $this->unique)) {
                        if (($key = array_search($columnName, $this->unique)) !== false) {
                            unset($this->unique[$key]);
                        }
                    }
                }
            }
        }
    }

    private function formatUniqueRowData()
    {
        $includeUniques = true;
        $includeNonUniques = false;
        return $this->formatRowData($includeUniques, $includeNonUniques);
    }

    private function formatNonUniqueRowData()
    {
        $includeUniques = false;
        $includeNonUniques = true;
        return $this->formatRowData($includeUniques, $includeNonUniques);
    }

    private function formatRowData($includeUniques = true, $includeNonUniques = true)
    {
        $data = [];
        foreach($this->rowData as $column => $value)
        {
            if (true === $includeUniques && (count($this->unique) <= 0 || in_array($column, $this->unique)))
            {
                $data[$column] = $value;
            }
            if (true === $includeNonUniques && count($this->unique) > 0 && ! in_array($column, $this->unique))
            {
                // if no unique columns provided, use all columns as unique
                $data[$column] = $value;
            }
        }
        return $data;
    }

    private function updateCounters($record)
    {
        if ($record->exists) {
            $this->counters['existing']++;
        } else
        {
            $record->save();
            $this->counters['new']++;
        }
    }

    private function displayStats()
    {
        if ($this->counters['existing'] > 0) {
            echo "\n" . 'Previously seeded: ' . $this->counters['existing'];
        }
        echo "\n" . 'Seeded: ' . $this->counters['new'];

        // for nicer formatting add a line return when done
        echo "\n\n";
    }

    protected function insertCallBack($record, $row) {}

}
