<?php
namespace Home\Controller;

class WBPartyMngController extends \Think\Controller {


 
	public function getRegionList() {
		if(isset($_POST['MaintainerCode']))	$condition['MaintainerCode'] = $_POST['MaintainerCode'];	
		$condition['PartyType'] = '分仓';			
		$condition['PartyEnabled'] = 1;			

		$pagestr = getInputValue("Page","1,1000");
		$fieldstr  = getInputValue("FieldStr","PartyCode as id,PartyName as value");
		
		$rs = M("bparty","",getMyCon())
		->field($fieldstr)
		->page($pagestr)
		->where($condition)
		->select();

		array_push($rs,array('id'=>'all','value'=>'所有'));
		$rs = array_reverse($rs);
		return $this -> ajaxReturn($rs);
	}
	
	public function getCWHList() {
		if(isset($_POST['MaintainerCode']))	$condition['MaintainerCode'] = $_POST['MaintainerCode'];	
		$condition['PartyType'] = '总仓';			
		$condition['PartyEnabled'] = 1;			

		$pagestr = getInputValue("Page","1,1000");
		$fieldstr  = getInputValue("FieldStr","PartyCode as id,PartyName as value");
		
		$rs = M("bparty","",getMyCon())
		->field($fieldstr)
		->page($pagestr)
		->where($condition)
		->select();

		return $this -> ajaxReturn($rs);
	}


		public function getRelPartyList() {

			$condition['ParentCode'] = getInputValue("RegionCode","D03A");
			$condition['RelationType'] = getInputValue("RelationType","归属关系");
			$condition['IsReplenish']=1;
			
			$pagestr = getInputValue("Page","1,1000");

			$fieldstr = "_Identify,ParentCode,ParentName,RelationType,PartyCode,PartyName,";
			$fieldstr = $fieldstr . "PartyType,PartyLevel,PartyEnabled,RepBatchSize,RepParaNextDate,RepParaOrderCycle,";
			$fieldstr = $fieldstr . "RepParaSupplyTime,RepParaRollSpan,IsReturnStock,RetBatchSize,IsRetOverStock,";
			$fieldstr = $fieldstr . "RetOverStockNextDate,RetOverStockCycle,IsRetDeadStock,RetDeadStockNextDate,";
			$fieldstr = $fieldstr . "RetDeadStockCycle,IsAdjustTarget,IsUseSKUAdjPara,AdjParaUpChkPeriod,AdjParaUpFreezePeriod,";
			$fieldstr = $fieldstr . "AdjParaUpErodeLmt,AdjParaDnChkPeriod,AdjParaDnFreezePeriod,AdjParaDnErodeLmt";
		
			$fieldstr = getInputValue("FieldStr",$fieldstr);
			
			
			$dbt = M('vwp2partyrel','',getMyCon());
			
			$rs = $dbt
			->field($fieldstr)
			->where($condition)
			->page($pagestr)
			->select();
		
		return $this -> ajaxReturn($rs);
	}
	
//获得门店运作指标
	public function getPartyIndex() {
		if(isset($_POST['RegionCode'])) $condition['ParentCode'] = getInputValue("RegionCode","D03A");
		if(isset($_POST['StoreCode'])) $condition['PartyCode'] = getInputValue("StoreCode","A00Z003");
		
		$pagestr = getInputValue("Page","1,1000");
		$fieldstr = "_Identify,ParentCode,ParentName,PartyCode,PartyName,PartyType,PartyLevel,YearName,SeasonName,SeasonStageName,SeriesName,MiddleSizeNum,ShortNum,";
		$fieldstr = $fieldstr . "ShortRatio,ReplenishRatio,HotSKCNumInParent,HotSKCNumInParty,HotSKCRatioPartyCover,StockOnHandQty,StockOnRoadQty,StockTotalQty,StockDayOfInventory,StockStoreDeadGlobalHot,";
		$fieldstr = $fieldstr . "StockOverInStores,StockShortInStores,StockDailyIDD,SaleYesterday,Sale14Days,SaleTotal,SaleCompletePer,SaleDailyTDD";
		
		$fieldstr = getInputValue("FieldStr",$fieldstr);
		$rs = M('zdimparty','',getMyCon())
		->field($fieldstr)
		->where($condition)
		->page($pagestr)
		->select();
//		setTag('sql123', $dbt->_sql());

		return $this -> ajaxReturn($rs);
	}

  
	 
}
?>