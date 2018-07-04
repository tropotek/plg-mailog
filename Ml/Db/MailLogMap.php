<?php
namespace Ml\Db;

use App\Controller\Subscriber;
use Tk\Db\Tool;
use Tk\Db\Map\ArrayObject;
use Tk\DataMap\Db;
use Tk\DataMap\Form;
use Tk\Db\Mapper;

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
     * Find filtered records
     *
     * @param array $filter
     * @param Tool $tool
     * @return ArrayObject|Subscriber[]
     * @throws \Tk\Db\Exception
     */
    public function findFiltered($filter = array(), $tool = null)
    {
        $from = sprintf('%s a ', $this->getDb()->quoteParameter($this->getTable()));
        $where = '';

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
                $where .= '(' . substr($w, 0, -3) . ') AND ';
            }
        }

        if (!empty($filter['to'])) {
            $where .= sprintf('a.to = %s AND ', $this->getDb()->quote($filter['to']));
        }
        if (!empty($filter['from'])) {
            $where .= sprintf('a.from = %s AND ', $this->getDb()->quote($filter['from']));
        }
        if (!empty($filter['hash'])) {
            $where .= sprintf('a.hash = %s AND ', $this->getDb()->quote($filter['hash']));
        }

        if (!empty($filter['exclude'])) {
            if (!is_array($filter['exclude'])) $filter['exclude'] = array($filter['exclude']);
            $w = '';
            foreach ($filter['exclude'] as $v) {
                $w .= sprintf('a.id != %d AND ', (int)$v);
            }
            if ($w) {
                $where .= ' ('. rtrim($w, ' AND ') . ') AND ';
            }
        }
        
        if ($where) {
            $where = substr($where, 0, -4);
        }

        $res = $this->selectFrom($from, $where, $tool);
        return $res;
    }
}