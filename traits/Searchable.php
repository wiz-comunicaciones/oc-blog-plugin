<?php namespace Wiz\Blog\Traits;

use Exception;

trait Searchable
{
    /**
     * @var array List of attribute names which should be ssearchable (as defined in database table index creation).
     *
     * protected $searchable = [];
     */

    /**
     * Boot the searchable trait for a model
     *
     * @return void
     */
    public static function bootSearchable()
    {
        if (!property_exists(get_called_class(), 'searchable')) {
            throw new Exception(sprintf(
                'You must define a $searchable property in %s to use the Searchable trait.', get_called_class()
            ));
        }
    }

    /**
     * Replaces spaces with full text search wildcards
     *
     * @param string $term
     * @return string
     */
    protected function fullTextWildcards($term)
    {
        // removing symbols used by MySQL
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);

        $words = explode(' ', $term);

        foreach($words as $key => $word) {
            /*
             * applying + operator (required word) only big words
             * because smaller ones are not indexed by mysql
             */
            if(strlen($word) >= 3) {
                $words[$key] = '+' . $word . '*';
            }
        }

        $searchTerm = implode( ' ', $words);

        return $searchTerm;
    }

    /**
     * Scope a query that matches a full text search of term.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $term)
    {
        $columns = implode(',',$this->searchable);

        $searchableTerm = $this->fullTextWildcards($term);

        $query->selectRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE) AS relevance_score", [$searchableTerm])
            ->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", $searchableTerm)
            ->orderByDesc('relevance_score');

        return $query;
    }
}