<?php
namespace Tk\Ml\Db;

use App\Controller\Subscriber;
use Tk\Db\Tool;
use Tk\Db\Map\ArrayObject;
use Tk\DataMap\Db;
use Tk\DataMap\Form;
use Bs\Db\Mapper;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class MailLogMap extends Mapper
{
    /**
     *
     * @return \Tk\DataMap\DataMap
     */
    public function getDbMap()
    {
        if (!$this->dbMap) {
            $this->setMarkDeleted('del');
            $this->setTable(\Tk\Ml\Plugin::$DB_TABLE);
            $this->dbMap = new \Tk\DataMap\DataMap();
            $this->dbMap->addPropertyMap(new Db\Integer('id'), 'key');
            $this->dbMap->addPropertyMap(new Db\Text('to'));
            $this->dbMap->addPropertyMap(new Db\Text('from'));
            $this->dbMap->addPropertyMap(new Db\Text('subject'));
            $this->dbMap->addPropertyMap(new Db\Text('body'));
            $this->dbMap->addPropertyMap(new Db\Text('hash'));
            $this->dbMap->addPropertyMap(new Db\Text('notes'));
            $this->dbMap->addPropertyMap(new Db\Date('created'));

        }
        return $this->dbMap;
    }

    /**
     *
     * @return \Tk\DataMap\DataMap
     */
    public function getFormMap()
    {
        if (!$this->formMap) {
            $this->formMap = new \Tk\DataMap\DataMap();
            $this->formMap->addPropertyMap(new Form\Integer('id'), 'key');
            $this->formMap->addPropertyMap(new Form\Text('to'));
            $this->formMap->addPropertyMap(new Form\Text('from'));
            $this->formMap->addPropertyMap(new Form\Text('subject'));
            $this->formMap->addPropertyMap(new Form\Text('body'));
            $this->formMap->addPropertyMap(new Form\Text('hash'));
            $this->formMap->addPropertyMap(new Form\Text('notes'));
        }
        return $this->formMap;
    }

    /**
     * @param array|\Tk\Db\Filter $filter
     * @param Tool $tool
     * @return ArrayObject|MailLog[]
     * @throws \Exception
     */
    public function findFiltered($filter, $tool = null)
    {
        return $this->selectFromFilter($this->makeQuery(\Tk\Db\Filter::create($filter)), $tool);
    }

    /**
     * @param \Tk\Db\Filter $filter
     * @return \Tk\Db\Filter
     */
    public function makeQuery(\Tk\Db\Filter $filter)
    {
        $filter->appendFrom('%s a', $this->quoteParameter($this->getTable()));

        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->getDb()->escapeString($filter['keywords']) . '%';
            $w = '';
            $w .= sprintf('a.to LIKE %s OR ', $this->getDb()->quote($kw));
            $w .= sprintf('a.from LIKE %s OR ', $this->getDb()->quote($kw));
            $w .= sprintf('a.subject LIKE %s OR ', $this->getDb()->quote($kw));
            $w .= sprintf('a.body LIKE %s OR ', $this->getDb()->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('a.id = %d OR ', $id);
            }
            if ($w) {
                $filter->appendWhere('(%s) AND ', substr($w, 0, -3));
            }
        }

        if (!empty($filter['to'])) {
            $filter->appendWhere('a.to = %s AND ', $this->getDb()->quote($filter['to']));
        }
        if (!empty($filter['from'])) {
            $filter->appendWhere('a.from = %s AND ', $this->getDb()->quote($filter['from']));
        }
        if (!empty($filter['hash'])) {
            $filter->appendWhere('a.hash = %s AND ', $this->getDb()->quote($filter['hash']));
        }

        if (!empty($filter['exclude'])) {
            $w = $this->makeMultiQuery($filter['exclude'], 'a.id', 'AND', '!=');
            if ($w) $filter->appendWhere('(%s) AND ', $w);
        }

        return $filter;
    }

}