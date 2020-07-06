<?php


class QueryBuilder
{

    public function escape($data){
            if ( !isset($data) || empty($data)) return '';
            if (is_numeric($data)) return $data;

            $non_displayables = array(
                '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
                '/%1[0-9a-f]/',             // url encoded 16-31
                '/[\x00-\x08]/',            // 00-08
                '/\x0b/',                   // 11
                '/\x0c/',                   // 12
                '/[\x0e-\x1f]/'             // 14-31
            );
            foreach ( $non_displayables as $regex )
                $data = preg_replace( $regex, '', $data );
            $data = str_replace("'", "''", $data );
            return $data;
    }


    public function organizeColumns($columns = array()){
        $columnsEscaped = [];
        for ($i = 0; $i < count($columns); $i++)
            $columnsEscaped[] = $this->escape($columns[$i]);

        return implode(',', $columnsEscaped);
    }


}