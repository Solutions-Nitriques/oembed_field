<?php

/**
 * @package toolkit
 */
/**
 * Specialized EntryQueryFieldAdapter that facilitate creation of queries filtering/sorting data from
 * an oEmbed Field.
 * @see FieldoEmbed
 * @since Symphony 3.0.0
 */
class EntryQueryoEmbedAdapter extends EntryQueryFieldAdapter
{
    protected function filterSingle(EntryQuery $query, $filter)
    {
        General::ensureType([
            'filter' => ['var' => $filter, 'type' => 'string'],
        ]);

        $columns = ['url', 'title', 'driver'];

        if ($this->isFilterRegex($filter)) {
            return $this->createFilterRegexp($filter, $columns);
        } elseif ($this->isFilterSQL($filter)) {
            return $this->createFilterSQL($filter, $columns);
        } elseif ($this->isFilterNotEqual($filter)) {
            return $this->createFilterNotEqual($filter, $columns);
        }
        return $this->createFilterEquality($filter, $columns);
    }

    public function getSortColumns()
    {
        return ['title'];
    }
}
