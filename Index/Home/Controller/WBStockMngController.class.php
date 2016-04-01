<?php
namespace Home\Controller;

class WBStockMngController extends \Think\Controller {

		// 获得门店、分仓和中央仓的目标库存及库存
		public function getFGWarehouseTSInfo(){
		$storecode = getInputValue("WHCode","A00Z003");
			
		$Model = new \Think\Model("","",getMyCon());
		
		$sqlstr = "SELECT dstock._Identify,PartyCode,dstock.SKUCode,productcolorcode,colorname,SizeName,brandname,yearname,seasonname,seasonstagename,";
		$sqlstr = $sqlstr . " maintypename,subtypename,TargetQty,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) as StockQty,";
		$sqlstr = $sqlstr . " (ifnull(TargetQty,0)-ifnull(OnHandQty,0)-ifnull(OnRoadQty,0)) as RepRetQty";
		$sqlstr = $sqlstr . " FROM dstock left join bsku on dstock.SKUCode = bsku.skucode";
		$sqlstr = $sqlstr . " where PartyCode='" . $storecode . "' and (ifnull(TargetQty,0)+ifnull(OnHandQty,0)+ifnull(OnRoadQty,0))>0";

		$rs=$Model->query($sqlstr);

		return $this -> ajaxReturn($rs);
		}
		
		
		// 获得分仓和中央仓的目标库存及库存
 	public function getDCWHTSInfo() {
		$whcode = getInputValue("WHCode","A00Z003");
		
		$Model = new \Think\Model("","",getMyCon());
		
		$sqlstr = "SELECT PartyCode,dstock.SKUCode,productcolorcode,colorname,SizeName,brandname,yearname,seasonname,seasonstagename,";
		$sqlstr = $sqlstr . " maintypename,subtypename,TargetQty,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) as StockQty,";
		$sqlstr = $sqlstr . " (ifnull(TargetQty,0)-ifnull(OnHandQty,0)-ifnull(OnRoadQty,0)) as RepRet";
		$sqlstr = $sqlstr . " FROM dstock left join bsku on dstock.SKUCode = bsku.skucode";
		$sqlstr = $sqlstr . " where PartyCode='" . $whcode . "'";

		$rs=$Model->query($sqlstr);

		return $this -> ajaxReturn($rs);
		}
	
	//获得目标库存调整记录
	public function getPartyAdjRec() {
		if(isset($_POST['WHCode'])) $condition['dadjusttsrecord.PartyCode'] = getInputValue("WHCode","ZZ27097");		
		if(isset($_POST['EndDate'])) $condition['RecordDate'] = array("elt",getInputValue("EndDate","2014-05-02"));			
		if(isset($_POST['StartDate'])) $condition['RecordDate'] = array("egt",getInputValue("StartDate","2014-04-02"));			
		
		$pagestr = getInputValue("Page","1,100");
		
		$fieldstr = "PartyName,SKUCode,RecordDate,OldTargetQty,SugTargetQty,AdjustReason,operator";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M("dadjusttsrecord","",getMyCon())
        ->join("left join bparty as p1 on dadjusttsrecord.partycode = p1.partycode")
//      ->join("left join bsku as p2 on dadjusttsrecord.skucode = p2.skucode")
        ->field($fieldstr)
        ->page($pagestr)
		->order("dadjusttsrecord._Identify desc")
        ->where($condition)
        ->select();
		
		return $this -> ajaxReturn($rs);
	}
	
		
	//获得补货退货单
	public function getRepRetOrder() {
		if(getInputValue("OrderType","Rep")=='Rep'){$tablename='dreporder';}else{$tablename='dretorder';};
		if(isset($_POST['RegionCode']))  $condition['d1.ParentCode'] = getInputValue("RegionCode","D03A");			
		if(isset($_POST['WHCode']))  $condition['d1.PartyCode'] = getInputValue("WHCode","ZZ27097");			
		if(isset($_POST['EndDate']))  $condition['MakeDate'] = array("elt",getInputValue("EndDate","2014-05-21"));			
		if(isset($_POST['StartDate']))  $condition['MakeDate'] = array("egt",getInputValue("StartDate","2014-03-24"));			
		if(isset($_POST['WHType']))  $condition['p1.PartyType'] = getInputValue("WHType","门店");			
		
		$pagestr = getInputValue("Page","1,1000");
		
		$fieldstr = "p1.PartyName,p2.PartyName as ParentName,d1.OrderCode,MakeDate,sum(OrderQty) as OrderQty";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M($tablename . " as d1","",getMyCon())
        ->join("left join bparty as p1 on d1.partycode = p1.partycode")
        ->join("left join bparty as p2 on d1.parentcode = p2.partycode")
        ->field($fieldstr)
        ->page($pagestr)
        ->group("p1.PartyName,p2.PartyName,d1.OrderCode,MakeDate")
        ->where($condition)
        ->select();
		
		return $this -> ajaxReturn($rs);
	}


	//获得补货单明细
	public function getRepRetOrderItem() {
		if(getInputValue("OrderType","Rep")=='Rep'){$tablename='dreporder';}else{$tablename='dretorder';};
		if(isset($_POST['OrderCode']))  $condition['OrderCode'] = getInputValue("OrderCode","ZZ27001@2014-05-03");			
		
		$fieldstr = "p1.PartyName,p2.PartyName as ParentName,d1.OrderCode,d1.SKUCode,ProductColorCode,ProductName,ColorName,";
		$fieldstr = $fieldstr . "SizeName,BrandName,YearName,SeasonName,SeasonStageName,MainTypeName,SubTypeName,OrderType,OrderQty,MakeDate";
		$fieldstr  = getInputValue("FieldStr",$fieldstr);
		
        $rs = M($tablename . " as d1","",getMyCon())
        ->join("left join bparty as p1 on d1.partycode = p1.partycode")
        ->join("left join bparty as p2 on d1.parentcode = p2.partycode")
		->join("left join bsku as p3 on d1.skucode = p3.skucode")
        ->field($fieldstr)
        ->where($condition)
        ->select();
		
//		p(M($tablename . " as d1","",getMyCon())->_sql());
		return $this -> ajaxReturn($rs);
	}
		
	//获得产品的历史库存
	public function getProdHSStock() {
		$condition['PartyCode'] = getInputValue("WHCode","D03A");			
		$condition['SKUCode'] = getInputValue("SKUCode","133680012016573");
		$rs = M("dhisstock","",getMyCon())
		->order("HSRecordDate desc")
		->limit(30)
		->where($condition)
		->select();

		return $this -> ajaxReturn($rs);
	}
	
	//显示门店的库存结构
	   public function getStoreStockStruct(){
     	$partycode = getInputValue("StoreCode","A00Z003");
		
		$sqlstr = "select PartyCode,yearname,SeasonName,seriesname,count(1) as 'SKCNum',";
		$sqlstr = $sqlstr . " sum(ifnull(TargetQty,0)) as 'TargetQty',sum(ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) )as 'TotalQty',";
		$sqlstr = $sqlstr . " sum(ifnull(StoreOverStockQty,0) )as 'OverStockQty',sum(ifnull(StoreShortStockQty,0)) as 'ShortStockQty',";
		$sqlstr = $sqlstr . " sum(if(IsDeadProduct,0,ifnull(OnHandQty,0)+ifnull(OnRoadQty,0))) as 'DeadSKCNum',";
		$sqlstr = $sqlstr . " sum(if(IsDeadProduct,0,1)) as 'DeadSKCNum',sum(if(SaleType='畅销款',1,0)) as 'FastRunnerSKCNum'";
		$sqlstr = $sqlstr . " from dskcanalysis ";
		$sqlstr = $sqlstr . " where PartyCode='A00Z003'  and ifnull(OnHandQty,0)+ifnull(OnRoadQty,0) >0 ";
		$sqlstr = $sqlstr . " group by PartyCode,yearname,SeasonName,seriesname";
		
		$dbt = new \Think\Model("","",getMyCon());
		$rs = $dbt->query($sqlstr);
		
		return $this -> ajaxReturn($rs);
     }
}
?>