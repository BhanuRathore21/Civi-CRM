<?php

/**
 * 
 * @staticvar type $sid_year
 * 
 * based on the year filter we are generating the condition year wise and 
 * the year condition
 * 
 * @return string|boolean
 */
function _getSIDYear(){
  static $sid_year = null;

  if(isset($sid_year)) return $sid_year;
  
  if(!isset($_GET['tid'])) return null;
  
  $search_terms = $_GET['tid'];
  $errors1 = array_filter($search_terms);
  
  if(!in_array('all', $search_terms)){
    $terms = taxonomy_term_load_multiple($search_terms);
    $year = $term->name;
    if($year == null){
      $year = $search_term;
    }
    
    $term_year = '';
    foreach($terms as $term){
      $year = $term->name;

      if($term_year != ''){
        $term_year .= " OR ";
      }

      $term_year .= " YEAR(FROM_UNIXTIME(submitted)) = '{$year}'";
    }

    if($term_year){
      $sql = $sql .' AND ('. $term_year.') ';
    }
    
    
    $sid_year = $sql;
  } else {
    $sid_year = false;
  }
  
  return $sid_year;
}

/**
 * 
 * @staticvar type $sid_categories
 * 
 * 
 * 
 * @return array|boolean
 */
function _getSIDforcategory(){
  
  static $sid_categories = null;

  if(isset($sid_categories)) return $sid_categories;
  
  if(!isset($_GET['tid_1'])) return null;
  
  $search_terms = $_GET['tid_1'];
  $errors2 = array_filter($search_terms);
  
  if(is_array($search_terms) && !in_array('all', $search_terms)){
    $wfcat_lists = array(
      181 => 'Best Overall Programming',
      192 => "Community Outreach",      
      41 => 'Club Administration',
      40 => array("FJMC - other", "FJMC Other"),
      191 => "Fundraising",      
      54 => "Hearing Men\'s Voices",
      188 => "Health and Wellness",
      182 => 'Hebrew Programming',
      187 => 'HMV',
      183 => 'Israel / Masorti',
      190 => "Jewish Observance",
      57 => 'Keruv',
      184 => array('Mens Club Shabbat', "Men\'s Club Shabbat"),
      51 => "Men\'s Health Programs",
      55 => 'Non-FJMC Program',
      50 => array('Programming for Young Men', 'Programming for Younger Men'),
      49 => array('Shomrei Haaretz', "Shomrei Ha\’aretz"),
      186 => 'Synagogue Service Project',
      52 => 'World Wide Wrap',
      189 => "Yellow Candle",
      185 => 'Yom HaShoah / Yellow Candle',
      48 => 'Youth Programming'
    );

    $flag = false;
    foreach($search_terms as $tid){

      if(isset($wfcat_lists[$tid])){
        
        $flag = true; 
        
        if($search_where_query != ''){
          $search_where_query .= " OR ";
        }

        $term_key = ':term_key_'.$tid;
        $term_where[$term_key] = $wfcat_lists[$tid];
        $wher = $wfcat_lists[$tid];
        if(is_array($wher)){
          $tmp_search_where_query = '';
          foreach($wher as $tmp_where){
            if($tmp_search_where_query != '')$tmp_search_where_query .= ' OR ';
            $tmp_search_where_query .= " `data` = '{$tmp_where}' ";
          }
          $search_where_query = $tmp_search_where_query;
        } else {
          $search_where_query .= " `data` = '{$wher}' ";
        }

        $sid_categories = array($search_where_query, $term_where);
      }
    }
    
    if($flag == false){
      $sid_categories = array(" `data` = ':term_key_0' ", array(':term_key_0' => '0'));
    }
    
  }
  return $sid_categories;
}


/**
 * 
 * @staticvar boolean $placement_flag
 * 
 * @webforms both 1019 and 5346 are doesn't have placement field. So if placement filter set,
 * wee need to remove the webform results from result set.
 * 
 * @return boolean
 */
function _getPlacementFlag(){
  static $placement_flag = false;

  if($placement_flag != false) return $placement_flag;
  
  if(!isset($_GET['tid_2'])) return false;
  
  $search_terms = $_GET['tid_2'];
  $errors3 = array_filter($search_terms);
  
  if(!in_array('all', $search_terms)){
    $placement_flag = true;
  }
  
  return $placement_flag;
}

/**
 * 
 * hook_views_query_alter(&$view, &$query);
 */
function club_details_views_query_alter(&$view, &$query){

  if($view->name == 'torch_award_listing' && $view->current_display == 'page'){
    $where  = $query->where;    
    $webform_node_ids = array(5346, 1019);  //webform node ids
    $submission_ids = $result_sids = array();
    
    $sidYear = _getSIDYear();
    $sidCategories = _getSIDforcategory();
    if($sidYear !== false || $sidCategories !== false || _getPlacementFlag() !== true ) {
      $sql = "SELECT DISTINCT(wfs.sid) FROM {webform_submissions} wfs LEFT JOIN {webform_submitted_data} as wfsd ON wfs.nid = wfsd.nid AND wfs.sid = wfsd.sid "
             . "WHERE wfs.nid IN (".implode(',', $webform_node_ids).") ";
      $sql = $sql . $sidYear;

      $result_sids = array();
      if(_getPlacementFlag()){
        //Fetch 'Torch Award Placement' taxonomy list
        $placements = array();
        if(($list = taxonomy_get_tree(6))){
          foreach($list as $item){
            $placements[$item->tid] = $item->name;
          }
        }
        //Fetch 'Torch Award Placement' taxonomy list
      
        $placement_filter = $_GET['tid_2'];
        if(!empty($placements)){
          $placement_where = '';
          foreach($placement_filter as $filter){
            $placement_where = $placement_where != '' ? $placement_where .' OR ' : '';
            $placement_where .= " wfsd.data = '{$placements[$filter]}' ";
          } 
          
          if($placement_where !== ''){
            $result = db_query($sql . " AND ($placement_where) ");
            $sids = $result->fetchAll();
            foreach($sids as $obj){
              $result_sids[$obj->sid] = $obj->sid;
            }
          }
        }
      }
      
      $result_cat_sids = array();
      if($sidCategories){
        list($qry, $where_placeholder) = $sidCategories;        
        if($qry){
          $result = db_query($sql.' AND ('.$qry.')', $where_placeholder);
          $sids = $result->fetchAll();
          foreach($sids as $obj){
            $result_cat_sids[$obj->sid] = $obj->sid;
      }
        }
      }
      
      if(!empty($result_sids) && !empty($result_cat_sids)){
        $result_sids = array_intersect_key($result_sids, $result_cat_sids);
      } else {
        $result_sids = $result_sids + $result_cat_sids;
      }
      
      if(!empty($result_sids)){
        $sql = $sql.' AND wfs.sid IN ('.implode(',', $result_sids).')';
        $result = db_query($sql);
      } else {
        //if some category selected and placement selected
        if((is_array($_GET['tid_1']) && !in_array('all', $_GET['tid_1']))){
          $sql = $sql.' AND wfs.sid = 0 ';
      }
        //if some category selected and placement selected
        
        $result = db_query($sql);
      }
      $sids = $result->fetchAll();

      foreach($sids as $obj){
        $submission_ids[] = $obj->sid;
      }
    }

    if(!empty($submission_ids)){
      $submission_ids = array_unique($submission_ids);
      
        $cnt = count($where);
        $query->where[($cnt-1)]['conditions'][] = array(
          'field' => 'webform_submissions_node.sid',
        'value' => $submission_ids,
          'operator' => 'in'
        );

        $query->where[$cnt]['conditions'][] = array(
          'field' => 'webform_submissions_node.sid',
        'value' => $submission_ids,
          'operator' => 'in'
        );
    } else if( ( isset($_GET['tid']) && !in_array('all', $_GET['tid']) ) 
        || ( isset($_GET['tid_1']) && !in_array('all', $_GET['tid_1']) ) 
        || ( isset($_GET['tid_2']) && !in_array('all', $_GET['tid_2']) ) ) {
      
      $cnt = count($where);      
      $query->where[($cnt-1)]['conditions'][] = array(
        'field' => 'webform_submissions_node.sid',
        'value' => '',
        'operator' => '='
      );

      $query->where[$cnt]['conditions'][] = array(
        'field' => 'webform_submissions_node.sid',
        'value' => '',
        'operator' => '='
      );
    }

    //intialize the civicim if not
    if (!defined('CIVICRM_SETTINGS_PATH')) civicrm_initialize();
    
    $daoObj = new CRM_Core_DAO();
    $database = $daoObj->database();

    $ctitle_field  = " IF(webform_submissions_node.sid IS NULL, node.title, 
                          (SELECT display_name FROM {$database}.civicrm_contact WHERE id = 
                              IF( webform_submitted_data_webform_submissions.data IS NULL, 
                                  webform_submitted_data_webform_submissions_2.data, 
                                  webform_submitted_data_webform_submissions.data
                              )
                          )
                      )";

    $program_field = "IF( webform_submissions_node.sid IS NOT NULL, 
                          IF( webform_submitted_data_webform_submissions_1.data IS NULL, 
                              webform_submitted_data_webform_submissions_3.data, 
                              webform_submitted_data_webform_submissions_1.data
                          ), NULL
                      ) ";
    $query->add_field(null, $ctitle_field, 'ctitle'); //for club title from civicrm db
    $query->add_field(null, $program_field, 'program_title'); //for program name from webform
    #$query->add_field(null, "IF(webform_submissions_node.sid IS NULL, node.title, IF( webform_submitted_data_webform_submissions.data IS NULL, webform_submitted_data_webform_submissions_1.data, webform_submitted_data_webform_submissions.data))", 'ctitle');
    
    $query->orderby[] = array(
      'field' => 'ctitle',
      'direction' => 'ASC'
    );

    /********** exclude unnecessary tables and condition form views query **********/
    $exclude_all_tables = array();
    
    if(isset($_GET['tid']) && in_array('all', $_GET['tid'])){
      $exclude_all_tables[] = 'taxonomy_index.tid';
    }
    
    if(isset($_GET['tid_1']) && in_array('all', $_GET['tid_1'])){
      $exclude_all_tables[] = 'taxonomy_index2.tid';
    }
    
    if(isset($_GET['tid_2']) && in_array('all', $_GET['tid_2'])){
      $exclude_all_tables[] = 'taxonomy_index3.tid';
    }

    if(!empty($exclude_all_tables)){
      foreach($query->where[1]['conditions'] as $key => $where_cond){
        if(in_array($where_cond['field'], $exclude_all_tables)){
          if($where_cond['value'] == 'all'){

            list($tbl, $field) = explode('.', $where_cond['field']);
            unset($query->table_queue[$tbl]);
            unset($query->tables['node'][$tbl]);
            unset($query->where[1]['conditions'][$key]);
          }
        }
      }
    }

    foreach($query->table_queue as $tbl => $arr) {
      if(in_array($tbl , array('taxonomy_index', 'taxonomy_index2', 'taxonomy_index3'))){
        
        if(isset($query->table_queue[$tbl]['join']) && is_array($query->table_queue[$tbl]['join']->extra)){
          foreach($query->table_queue[$tbl]['join']->extra as $index => $cond){
            if($cond['value'] == 'all'){
              unset($query->table_queue[$tbl]['join']->extra[$index]);
            }
          }
        }
      }
    }
    /********** exclude unnecessary tables and condition form views query **********/
  }
}
