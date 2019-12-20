<?php

declare(strict_types = 1);
namespace App;


class Paginator
{
    private $_page;
    private $_limit;
    private $_total;
    private $_dataSource;

    /**
     * Paginator constructor.
     * @param array $dataSource
     */
    public function __construct($dataSource, $limit)
    {
        $this->_limit = $limit;
        $this->_dataSource = $dataSource;
        $this->_total = count($dataSource);
        $this->_page = 1;
    }

    public function getPagedData($page)
    {
        $this->_page = $page;
        return array_slice($this->_dataSource, ($this->_page - 1) * $this->_limit, $this->_limit);
    }

    public function createLinks( $links, $list_class ) {
        if ( $this->_limit == 'all' ) {
            return '';
        }

        $last       = ceil( $this->_total / $this->_limit );

        $start      = ( ( $this->_page - $links ) > 0 ) ? $this->_page - $links : 1;
        $end        = ( ( $this->_page + $links ) < $last ) ? $this->_page + $links : $last;

        $html       = '<ul class="' . $list_class . '">';

        $class      = ( $this->_page == 1 ) ? "disabled" : "";
        $html       .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . ( $start <= $this->_page - 1 ? $this->_page - 1 : $start ) . '">&laquo;</a></li>';

        if ( $start > 1 ) {
            $html   .= '<li><a href="?limit=' . $this->_limit . '&page=1">1</a></li>';
            $html   .= '<li class="disabled"><span>...</span></li>';
        }

        for ( $i = $start ; $i <= $end; $i++ ) {
            $class  = ( $this->_page == $i ) ? "active" : "";
            $html   .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . $i . '">' . $i . '</a></li>';
        }

        if ( $end < $last ) {
            $html   .= '<li class="disabled"><span>...</span></li>';
            $html   .= '<li><a href="?limit=' . $this->_limit . '&page=' . $last . '">' . $last . '</a></li>';
        }

        $class      = ( $this->_page == $last ) ? "disabled" : "";
        $html       .= '<li class="' . $class . '"><a href="?limit=' . $this->_limit . '&page=' . ( $this->_page + 1 <= $last ? $this->_page + 1 : $last) . '">&raquo;</a></li>';

        $html       .= '</ul>';

        return $html;
    }
}