<?php

namespace App\Services;

use Illuminate\Http\Request;

class PostQuery {
    protected $safeParms = [
        'parent_id' => ['eq'],
        'title' => ['eq'],
        'content' => ['eq', 'like'], 
        'likes' => ['eq', 'gt', 'lt', 'gte', 'lte'],
        'replies' => ['eq', 'gt', 'lt', 'gte', 'lte']
    ];

    // no columnMap here because there is no changes in column's names

    protected $operatorMap = [
        'eq' => '=',
        'gt' => '>',
        'lt' => '<',
        'gte' => '>=',
        'lte' => '<=',
        'like' => 'LIKE'
    ];

    public function transform(Request $request) {
        $eloQuery = [];

        foreach ($this->safeParms as $parm => $operators) {
            $query = $request->query($parm);

            if (!isset($query)) {
                continue;
            }

            // no column Map so : 
            $column = $parm;

            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }


        return $eloQuery;
    }
}