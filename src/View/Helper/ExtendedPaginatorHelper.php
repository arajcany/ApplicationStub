<?php

namespace App\View\Helper;

use Cake\View\Helper\PaginatorHelper as CakePaginatorHelper;

/**
 * Paginator helper
 */
class ExtendedPaginatorHelper extends CakePaginatorHelper
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $templates = [
            'nextActive' => '<li class="page-item next"><a class="page-link" rel="next" href="{{url}}">{{text}}</a></li>',
            'nextDisabled' => '<li class="page-item next disabled"><a class="page-link" href="" onclick="return false;">{{text}}</a></li>',
            'prevActive' => '<li class="page-item prev"><a class="page-link" rel="prev" href="{{url}}">{{text}}</a></li>',
            'prevDisabled' => '<li class="page-item prev disabled"><a class="page-link" href="" onclick="return false;">{{text}}</a></li>',
            'counterRange' => '{{start}} - {{end}} of {{count}}',
            'counterPages' => '{{page}} of {{pages}}',
            'first' => '<li class="page-item first"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'last' => '<li class="page-item last"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'number' => '<li><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'current' => '<li class="page-item active"><a class="page-link" href="">{{text}}</a></li>',
            'ellipsis' => '<li class="ellipsis">&hellip;</li>',
            'sort' => '<a href="{{url}}">{{text}}</a>',
            'sortAsc' => '<a class="asc" href="{{url}}">{{text}}</a>',
            'sortDesc' => '<a class="desc" href="{{url}}">{{text}}</a>',
            'sortAscLocked' => '<a class="asc locked" href="{{url}}">{{text}}</a>',
            'sortDescLocked' => '<a class="desc locked" href="{{url}}">{{text}}</a>',
        ];
        $this->setTemplates($templates);
    }

    public function prev($title = '< Previous', array $options = [])
    {
        return parent::prev($title, $options);
    }

    public function next($title = 'Next >', array $options = [])
    {
        return parent::next($title, $options);
    }

    public function first($first = '<< First', array $options = [])
    {
        return parent::first($first, $options);
    }

    public function last($last = 'Last >>', array $options = [])
    {
        return parent::last($last, $options);
    }


}
