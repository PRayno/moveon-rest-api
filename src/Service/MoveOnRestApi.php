<?php

namespace PRayno\MoveOnRestApi;


class MoveOnRestApi
{
    protected int $maxRowsPerQuery=250;

    public function __construct(private readonly MoveOnRestApiClient $moveOnRestApiClient)
    {}

    public function search(string $object,array $criteria,$limit=null,$page=null):array {

        $queryBuilder=[];
        foreach ($criteria as $criterion) {
            $queryBuilder[$criterion["field"].(isset($criterion["operator"]) ? "[".$criterion["operator"]."]" : "")]=$criterion["value"];
        }
        $query = $object."?";

        if ($limit !== null && $limit < $this->maxRowsPerQuery)
        {
            $rows=$limit;
            $totalRows=$rows;
            $totalPages=1;
        }
        else
        {
            if (!is_null($limit))
            {
                $totalRows = $limit;
                $totalPages = ceil($limit/$this->maxRowsPerQuery);
            }
            else
            {
                // On exécute une première query
                $queryBuilder["limit"] = 1;
                $response = $this->moveOnRestApiClient->request("GET",$query.http_build_query($queryBuilder))->toArray();
                $totalRows = $response["total"];

                $totalPages = ceil($totalRows/$this->maxRowsPerQuery);
            }

            $rows=$this->maxRowsPerQuery;
        }

        $queryBuilder["limit"] = $rows;

        $content=[];
        for ($i=1;$i<=$totalPages;$i++)
        {
            if ($i==$totalPages)
                $queryBuilder["limit"] = $totalRows-$rows*($totalPages-1);

            $queryBuilder["page"] = $i;
            $response = $this->moveOnRestApiClient->request("GET",$query.http_build_query($queryBuilder))->toArray();
            $content = array_merge($content,$response["data"]);
        }

        return $content;
    }

    public function update(string $object,int $id,array $content): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->moveOnRestApiClient->request("PATCH","$object/".$id,[
            'json' => $content,
        ]);
    }
}