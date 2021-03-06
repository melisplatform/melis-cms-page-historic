<?php 

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoric\Model\Tables;

use Laminas\Db\Sql\Expression;
use Laminas\Db\TableGateway\TableGateway;
use MelisEngine\Model\Tables\MelisGenericTable;

use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Where;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Laminas\Db\Sql\Predicate\Like;
use Laminas\Db\Sql\Predicate\Operator;
use Laminas\Db\Sql\Predicate\Predicate;

class MelisPageHistoricTable extends MelisGenericTable
{
    /**
     * Table name
     */
    const TABLE = 'melis_hist_page_historic';
    /**
     * Primary key
     */
    const PRIMARY_KEY = 'hist_page_id';

    /**
     * MelisPageHistoricTable constructor.
     */
	public function __construct()
	{
		$this->idField = self::PRIMARY_KEY;
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
	            $filters = array(new PredicateSet($likes,PredicateSet::COMBINED_BY_OR), new \Laminas\Db\Sql\Predicate\Expression($dateFilterSql));
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
            $select->where(['usr_id' => $user]);
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
        $select->columns(array(new \Laminas\Db\Sql\Expression('DISTINCT(hist_page_id) as pageId'), 'hist_id'));
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

    /**
     * Serves Select2-usable data (ex. User search filters)
     * @param array $where
     * @return mixed
     */
    public function getBOUsers(array $where = [
        'search' => null,
        'searchableColumns' => ['*'],
        'orderBy' => 'usr_firstname',
        'orderDirection' => null,
        'start' => null,
        'limit' => null,
    ])
    {
        $select = $this->tableGateway->getSql()->select();
        $select->columns(["fullname" => new Expression("DISTINCT(CONCAT(usr_firstname, ' ', usr_lastname))")]);
        $select->join('melis_core_user',
            'melis_core_user.usr_id = melis_hist_page_historic.hist_user_id',
            [
                'usr_id',
                'usr_login',
                'usr_email',
                'usr_firstname',
                'usr_lastname',
            ],
            $select::JOIN_LEFT
        );

        if (!empty($where['searchableColumns'])) {
            $searchWhere = new Where();
            $nest = $searchWhere->nest();

            foreach ($where['searchableColumns'] as $column) {
                $nest->like($column, '%' . $where['search'] . '%')->or;
            }
            $select->where($searchWhere);
        }

        if (!empty($where['limit'])) {
            $select->limit($where['limit']);
        }

        if (!empty($where['start'])) {
            $select->offset($where['start']);
        }

        if (!empty($where['orderBy']) && !empty($where['orderDirection'])) {
            $select->order($where['orderBy'] . ' ' . $where['orderDirection']);
        }

        return $this->tableGateway->selectWith($select);
    }
}
