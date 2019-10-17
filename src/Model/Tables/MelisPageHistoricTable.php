<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoric\Model\Tables;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use MelisEngine\Model\Tables\MelisGenericTable;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Predicate\Like;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\Predicate;

class MelisPageHistoricTable extends MelisGenericTable
{
    protected $tableGateway;
    protected $idField;
    
	public function __construct(TableGateway $tableGateway)
	{
		parent::__construct($tableGateway);
		$this->idField = 'hist_page_id';
	}

	/**
	 * Get Page Historic Data for MelisCms
	 * @param array $options
	 * @param string $fixedCriteria
	 */
	public function getPageHistoricData(array $options, $fixedCriteria = null, $user = null, $action = null)
	{
	    $select = $this->tableGateway->getSql()->select();
	    $result = $this->tableGateway->select();
	    $select->join('melis_core_user', 'melis_core_user.usr_id = melis_hist_page_historic.hist_user_id', ['fullname' => new Expression("CONCAT(usr_firstname, ' ', usr_lastname)")], $select::JOIN_INNER);

	    $where = !empty($options['where']['key']) ? $options['where']['key'] : '';
	    $whereValue = !empty($options['where']['value']) ? $options['where']['value'] : '';
	
	    $order = !empty($options['order']['key']) ? $options['order']['key'] : '';
	    $orderDir = !empty($options['order']['dir']) ? $options['order']['dir'] : 'ASC';
	
	    $start = (int) $options['start'];
	    $limit = (int) $options['limit'] === -1 ? $this->getTotalData() : (int) $options['limit'];
	
	    $columns = $options['columns'];
	
	    // check if there's an extra variable that should be included in the query
	    $dateFilter = $options['date_filter'];
	    $dateFilterSql = '';
	
	    if(count($dateFilter)) {
	        if(!empty($dateFilter['startDate']) && !empty($dateFilter['endDate'])) {
	            $dateFilterSql = '`' . $dateFilter['key'] . '` BETWEEN \'' . $dateFilter['startDate'] . ' 00:00:00' . '\' AND \'' . $dateFilter['endDate'] . ' 23:59:59\'';
	        }
	    }
	
	    // this is used when searching
	    if(!empty($where)) {
	        $w = new Where();
	        $p = new PredicateSet();
	        $filters = array();
	        $likes = array();
	        foreach($columns as $colKeys)
	        {
	            $likes[] = new Like($colKeys, ''.$whereValue.'');
	        }
	
	        if(!empty($dateFilterSql))
	        {
	            $filters = array(new PredicateSet($likes,PredicateSet::COMBINED_BY_OR), new \Zend\Db\Sql\Predicate\Expression($dateFilterSql));
	        }
	        else
	        {
	            $filters = array(new PredicateSet($likes,PredicateSet::COMBINED_BY_OR));
	        }
	        $fixedWhere = array(new PredicateSet(array(new Operator('', '=', ''))));
	        if(is_null($fixedCriteria))
	        {
	            $select->where($filters);
	        }
	        else
	        {
	            $select->where(array(
	                $fixedWhere,
	                $filters,
	            ), PredicateSet::OP_AND);
	        }
	    }

	    if ($user) {
            $select->where(new \Zend\Db\Sql\Predicate\Expression("CONCAT(usr_firstname, ' ', usr_lastname) = '" . $user . "'"));
        }

        if ($action) {
            $select->where(['hist_action' => $action]);
        }
	
	    // used when column ordering is clicked
	    if(!empty($order))
	        $select->order($order . ' ' . $orderDir);
	
	
        $getCount = $this->tableGateway->selectWith($select);
        $this->setCurrentDataCount((int) $getCount->count());


        // this is used in paginations
        $select->limit($limit);
        $select->offset($start);

        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
	}
	
	public function getHistoryByPageId($pageId)
	{
        return $this->getEntryById($pageId);
	}
	
	public function getDescendingHistoric($histIdPage, $max = null)
	{
	    $select = $this->tableGateway->getSql()->select();

	    $select->order('hist_id DESC');
	    $select->where(array('hist_page_id' => (int) $histIdPage));
	    if ($max)
	    	$select->limit($max);
	    $resultSet = $this->tableGateway->selectWith($select);
	
	    return $resultSet;
	}

	public function getHistoricById($histId, $max = 1)
	{
	    $select = $this->tableGateway->getSql()->select();

	    $select->order('hist_id DESC');
	    $select->where(array('hist_id' => (int) $histId));
	    if ($max)
	    	$select->limit($max);
	    $resultSet = $this->tableGateway->selectWith($select);

	    return $resultSet;
	}

    public function getPagesHistoricForDashboard($max = 5)
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(array(new \Zend\Db\Sql\Expression('DISTINCT(hist_page_id) as pageId'), 'hist_id'));
        $select->order('hist_id DESC');
        $select->limit($max);
        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;

    }

    /**
     * This will return all distinct actions
     * @param string $order
     * @return mixed
     */
    public function getPageHistoricListOfActions($order = 'ASC')
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(["action" => new Expression('DISTINCT(hist_action)')]);
        $select->order('hist_action' . ' '  . $order);
        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }

    /**
     * This will return all users
     * @return mixed
     */
    public function getUsers() {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(["fullname" => new Expression("DISTINCT(CONCAT(usr_firstname, ' ', usr_lastname))")]);
        $select->join('melis_core_user',
            'melis_core_user.usr_id = melis_hist_page_historic.hist_user_id',
            [],
            $select::JOIN_INNER
        );
        $resultSet = $this->tableGateway->selectWith($select);

        return $resultSet;
    }
}
