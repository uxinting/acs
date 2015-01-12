/**
 * ayGrid : JQuery Datagrid Control
 *
 * Copyright (c) 2011 Toly (http://www.54kao.com/)
 *                    
 * 
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) 
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * @requires jQuery v1.4+
 * @version 0.0.1
 * @todo load JSON data, etc.
 * 
 * Revision: 0.0.1.0 2010/12/26 GuiLin
 * 				Author : Toly,
 * 				Email  : bytoly@qq.com,
 *              description : http://www.54kao.com/aygrid
*/


if(jQuery)(
	function(jQuery){
		jQuery.extend(jQuery.fn,{
			ayGrid:function(options) {
				var ayGridFn;
				jQuery(this).each(function(){
					var settings = jQuery.extend({
					id              : jQuery(this).attr('id'), 
					columnHeader	: ['col1','col2','col3','col4'],
					columnHBColor	: '#666',//列头背景颜色
					columnHFColor	: '#000',//列头字体颜色
					columnType		: {'col1':'Label','col2':'Input','col3':'CombBox','col4':'CheckBox'},
					dataJson        : [{'col1':'vlaue11','col2':'vlaue12','col3':'v13','col4':'1'},
									   {'col1':'vlaue21','col2':'vlaue22','col3':'v23','col4':'0'}], 
					dataJsonUrl		: '',
					columnWidth		: {'col1':50,'col2':100,'col3':110,'col4':50}, 
					columnID		: ['col1','col2','col3','col4'],//
					rowKey			: null,//关键字段，和数据库的关键字段对照设置，便于数据修改和删除操作
					toolsDisplay	: false,//工具行是否显示
					toolsBgColor	: '#6FF',//工具行背景颜色
					toolsFontColor	: '#000',//工具行字体颜色
					pagesDisplay	: false,
					pagesBgColor	: '#69F',//页脚背景颜色
					pagesFontColor	: '#000',//页脚字体颜色
					columnListData	: {col3:{'v13':'vlaue13','v23':'vlaue23','v33':'vlaue33','v43':'vlaue43'}},
					columnListDataUrl: {},//{'col1':'ljson.php'}
					ajaxType		: "GET",//or POST
					gridWitdh		: 650,
					gridHeigh		: 450,
					gridBorderColor	: '#999',
					gridCFColor		: '#666',//单元格字体颜色
					gridCBColor		: '#FFF',//单元格背景颜色
					numberOfPage	: 10,
					cellClick		: function() {},
					gridSave		: function() {return false;},
					rowDelete		: function() {},
					onRowDrawing	: function() {},
					onCellChange	: function() {},
					onInit          : function() {}, 
					onCancel        : function() {}, 
					onError         : function() {}
				}, options);
					
				jQuery(this).data('settings',settings);	
				var workDom = jQuery(this);
				workDom.width(settings.gridWitdh);
				workDom.height(settings.gridHeigh);
				jQuery.each(settings.columnWidth,function(key , value){
														
							workDom.data(key + '_',value);
														});
				
				var ayGrid = {
					
					page			: 1,
					pages			: 1,
					numberOfPage	: settings.numberOfPage,
					rowCount		: 0,
					columnCount		: 0,
					gridWitdh		: settings.gridWitdh-2,
					gridHeigh		: settings.gridHeigh,
					gridBorderColor	: settings.gridBorderColor,
					gridCFColor		: settings.gridCFColor,
					gridCBColor		: settings.gridCBColor,
					enterPress		: false,
					columnMouseDown	: false,
					columnMouseIn	: false,
					columnMouseDX	: 0,
					columnWidth		: 0,
					columnSort		: [],
					dataWidth		: 0,
					scrollTop		: 0,
					scrollLeft		: 0,
					activeRow		: null,
					activeRowIndex	: 0,
					dataJsonUrl		: '',
					id				: settings.id,
					toolsDisplay	: settings.toolsDisplay,
					toolsBgColor	: settings.toolsBgColor,
					toolsFontColor	: settings.toolsFontColor,
					pagesDisplay	: settings.pagesDisplay,
					pagesBgColor	: settings.pagesBgColor,
					pagesFontColor	: settings.pagesFontColor,
					columnStyle		: null,
					columnCur		: null,
					columnWidth		: settings.columnWidth,
					gridData		: {},
					columnHeader	: settings.columnHeader,
					columnType		: settings.columnType,
					columnID		: settings.columnID,
					columnHBColor	: settings.columnHBColor,
					columnHFColor	: settings.columnHFColor,
					rowKey			: settings.rowKey,
					columnListDataUrl: settings.columnListDataUrl,
					ajaxType		: settings.ajaxType,
					columnListData	: settings.columnListData,
					cellClick		: settings.cellClick,
					CombBoxFocusFix	: false,
					ctrlKeyDown		: false,
					gridSave		: settings.gridSave,
					rowDelete		: settings.rowDelete,
					onRowDrawing	: settings.onRowDrawing,
					onCellChange	: settings.onCellChange,
					mask: function () {
						ayGrid.unmask();
	
						var op = {opacity: 0.8,z: 10000,bgcolor: '#ccc'};
						var original = jQuery('#' + ayGrid.id);
						var position = {top: 0, left: 0};
						
						position = original.position();

						var maskDiv = $('<div id="mask' + ayGrid.id + '"> </div>');
	
						maskDiv.appendTo(original);
	
						var maskWidth = original.outerWidth();
	
						if (!maskWidth) {
	
							maskWidth = original.width();
	
						}
	
						var maskHeight = original.outerHeight();
	
						if (!maskHeight) {
	
							maskHeight = original.height();
	
						}
	
						maskDiv.css({position: 'absolute',top: position.top,left: position.left,'z-index': op.z,width: maskWidth,height: maskHeight,'background-color': op.bgcolor,opacity: 0.8});
							
						var msgDiv = $('<div style="position:absolute;border:#6593cf 1px solid; padding:2px;background:#ccca;opacity: 1"><div style="line-height:24px;border:#a3bad9 1px solid;background:white;padding:2px 10px 2px 10px">' +'正在加载数据...' + '</div></div>');

						msgDiv.appendTo(maskDiv);

						var widthspace = (maskDiv.width() - msgDiv.width());

						var heightspace = (maskDiv.height() - msgDiv.height());

						msgDiv.css({cursor: 'wait',top: (heightspace / 2 - 2),left: (widthspace / 2 - 2)});
						maskDiv.fadeIn('fast', function () {
							$(this).fadeTo('slow', op.opacity);
							
						})
	
					},
	
					unmask: function () {
	
						var original = jQuery('#mask' + ayGrid.id );
						original.fadeOut('slow', 0, function () {
	
							$(this).remove();
	
						});
	
					},
					upGridData:function(){
						
						ayGrid.dataG.find("div").each(function(){
							$(this).find("div").each(function(){
							alert(jQuery(this).data('value'));								   
							});
							});

						
						},
					getColumListData:function(){

						jQuery.each(ayGrid.columnType,function(key,value){
							if(value=='CombBox' && ayGrid.columnListDataUrl[key] != undefined){
								jQuery.ajax({
									type: ayGrid.ajaxType,
									async: false,
									url: ayGrid.columnListDataUrl[key],
									contentType: "application/json; charset=utf-8",
									dataType: "json",
									cache: false,
									success: function (data) {
										var arrs= {},str = '',tarr;
										jQuery.each(data,function(key,dtRow){
											str = '';														   
											jQuery.each(dtRow,function(){
													
													str = str + this + '~!~';				   
				   
											 });
											tarr= str.split('~!~');
											arrs[tarr[0]] = tarr[1];

											});
										ayGrid.columnListData[key]=arrs;
										
						},
									error: function (err) {
									alert(err);
									}
								});
							}	
							
							});
						
						

						},
					sortColumn:function(sortType,Column){
						switch(sortType)
								{
									case 'desc':
										ayGrid.gridData.sort(function(a,b){return a[Column]>b[Column]?1:a[Column]==b[Column]?0:-1});
										
										break;
									case 'asc':
										ayGrid.gridData.sort(function(a,b){return a[Column]<b[Column]?1:a[Column]==b[Column]?0:-1});
										
										break;
								}
						
						ayGrid.createGrid('#'+ayGrid.id);
								
						
					},
					getGridData:function(){
						if(ayGrid.dataJsonUrl =='')return false;
							jQuery.ajax({
								type: ayGrid.ajaxType,
								async: true,
								url: ayGrid.dataJsonUrl,
								data: {beginN:ayGrid.page,countP:ayGrid.numberOfPage},
								contentType: "application/json; charset=utf-8",
								dataType: "json",
								cache: false,
								beforeSend:function(){
									ayGrid.mask();
									},
								success: function (data) {

									if(data != ''){
										ayGrid.gridData = data['data'];
										ayGrid.pages = Math.ceil(data['count'] / ayGrid.numberOfPage);
										//alert(ayGrid.pages);
										ayGrid.createGrid();
										
									}
									ayGrid.unmask();	
									
								},
								error: function (err) {
								alert(err);
								}
							});
						},
					setStyle:function(){
						var styleStr = '',i = 1,tab_width = 0,cWidth = 0,data_height = ayGrid.gridHeigh;
						
						if(ayGrid.columnStyle != null) ayGrid.columnStyle.remove();
						styleStr = '<style type="text/css">';
						jQuery.each(ayGrid.columnID,function(){
															
							cWidth = jQuery('#' + ayGrid.id).data(this + '_');
							styleStr = styleStr + '.c' + ayGrid.id + i + ' {width: ' + cWidth + 'px;height: 20px;font-size:12px;white-space: nowrap;background-color: ' + ayGrid.gridCBColor + ';color: ' + ayGrid.gridCFColor + ';margin-right: 1px;overflow: hidden;float: left;margin-bottom: 1px;vertical-align: bottom;}';
							tab_width = tab_width + cWidth + 1;
							i++;
						});
						
						data_height = data_height - 20;
						
						styleStr = styleStr + '.colt' + ayGrid.id +' {background-color: ' + ayGrid.columnHBColor + ';width: 100%;height: 100%;color: ' + ayGrid.columnHFColor + ';}';
						
						//styleStr = styleStr + '.rowHead' + ayGrid.id +' {width: 11px;height: 19px;font-size:12px;white-space: nowrap;background-color: #999;margin-right: 1px;overflow: hidden;float: left;margin-bottom: 1px;vertical-align: bottom;display:inline-block;}';
						//styleStr = styleStr + '.header' + ayGrid.id +' {position: absolute;}';
						
						styleStr = styleStr + '.column' + ayGrid.id +' {margin-bottom: 1px;}';
						
						//styleStr = styleStr + '.data' + ayGrid.id +' {top: 60px;height: 156px !important;position: absolute;overflow: auto;}';
						
						styleStr = styleStr + '.row' + ayGrid.id +' {margin-bottom: 1px;}';
						
						styleStr = styleStr + '.rowover' + ayGrid.id +' {background-color: #CCC;}';
						
						styleStr = styleStr + '.rowselected' + ayGrid.id +' {background-color: #F00;}';
						
						styleStr = styleStr + '.cellselected' + ayGrid.id +' {background-color: #0F0;}';
						
						styleStr = styleStr + '.pbuttonOut' + ayGrid.id +' {width: 45px;height: 14px;color: #000;font-size: 12px;text-align: center;}';
						
						styleStr = styleStr + '.pbuttonOn' + ayGrid.id +' {width: 45px;height: 14px;color: #000font-size: 12px;text-align: center;background-color: #CCC;cursor: pointer;}';						
						
						styleStr = styleStr + '.tools' + ayGrid.id +' {height: 20px;background-color: ' + ayGrid.toolsBgColor + ';color: ' + ayGrid.toolsFontColor + ';text-align: right;margin: 0;padding: 0;' + (ayGrid.toolsDisplay ? '' : ' display: none;') + '}';
						
						data_height = data_height - (ayGrid.toolsDisplay ? 20 : 0);
						
						styleStr = styleStr + '.tools' + ayGrid.id +' span {margin: 0;padding: 0;}';
						
						styleStr = styleStr + '.cpage' + ayGrid.id +' {height: 20px;background-color: ' + ayGrid.pagesBgColor + ';color: ' + ayGrid.pagesFontColor + ';margin: 0;' + (ayGrid.pagesDisplay ? '' : ' display: none;') + '}';
						
						data_height = data_height - (ayGrid.pagesDisplay ? 20 : 0);
						
						styleStr = styleStr + '#tb_header' + ayGrid.id + ' {width: ' + tab_width + 'px;background-color: #09F;border-top-width: 1px;border-bottom-width: 1px;border-left-width: 1px;border-top-style: solid;border-bottom-style: none;border-left-style: solid;border-top-color: #09F;border-bottom-color: #09F;border-left-color: #09F;position: relative;}';
						
						styleStr = styleStr + '#tb_data' + ayGrid.id + ' {width: ' + tab_width + 'px;background-color: #09F;border-top-width: 1px;border-bottom-width: 1px;border-left-width: 1px;border-top-style: solid;border-bottom-style: none;border-left-style: solid;border-top-color: #09F;border-bottom-color: #09F;border-left-color: #09F;}';
						
						styleStr = styleStr + '#tb_headerFrame' + ayGrid.id + ' {width: ' + ayGrid.gridWitdh + 'px;height: 20px;background-color: ' + ayGrid.gridBorderColor + ';overflow: hidden;position: relative;}';
						
						styleStr = styleStr + '#tb_dataFrame' + ayGrid.id + ' {width: ' + ayGrid.gridWitdh + 'px;height: ' + data_height + 'px;background-color: ' + ayGrid.gridBorderColor + ';overflow: auto;}';
						
						styleStr = styleStr + '#aytable' + ayGrid.id + ' {width: ' + ayGrid.gridWitdh + 'px;height: ' + ayGrid.gridHeigh + 'px;border: 1px solid #CCC;}';

						styleStr = styleStr + '</style>';
						
						ayGrid.columnStyle = jQuery(styleStr); 
						jQuery('head').append(ayGrid.columnStyle);

					},
					createGrid:function(){
						//alert(ayGrid.page);
						var ay_tb = jQuery('<div />'),
						tb_headerFrame = jQuery('<div class="headerFrame' + ayGrid.id + '" />'),
						tb_header = jQuery('<div class="header' + ayGrid.id + '" />'),
						tb_dataFrame = jQuery('<div class="dataFrame' + ayGrid.id + '" />'),
						tb_data = jQuery('<div class="data' + ayGrid.id + '" />'),
						tb_pageBar = jQuery('<div />'),
						hd_column = jQuery('<div class="column' + ayGrid.id + '" />'),
						row_str = '',i,j;
						
						jQuery('#' + ayGrid.id).empty();
						
						ay_tb.attr('id','aytable' + ayGrid.id);
						ayGrid.evens = ay_tb;
						tb_headerFrame.attr('id','tb_headerFrame' + ayGrid.id);
						tb_header.attr('id','tb_header' + ayGrid.id);
						tb_dataFrame.attr('id','tb_dataFrame' + ayGrid.id);
						tb_data.attr('id','tb_data' + ayGrid.id);
						ayGrid.dataG = tb_data;
						tb_pageBar.attr('id','tb_pagebar' + ayGrid.id);
						
						tb_header.append(hd_column);						
						tb_headerFrame.append(tb_header);
						ay_tb.append(tb_headerFrame);
						
						tb_dataFrame.append(tb_data);
						ay_tb.append(tb_dataFrame);
						
						ay_tb.append(tb_pageBar);
						
						jQuery('#' + ayGrid.id).append(ay_tb);
						
						ayGrid.getColumListData();
						
						i = 1;
						//hd_column.append('<span class="rowHead' + ayGrid.id +'"> </span>');
						jQuery.each(ayGrid.columnHeader,function(){
							var rw_column = jQuery('<div class="c' + ayGrid.id + i + '" />');
							
							rw_column.data('column',ayGrid.columnID[i-1]);
							
							rw_column.append('<div title="' + this + '" class="colt' + ayGrid.id + '">' + this + '</div>');
							hd_column.append(rw_column); 
							ayGrid.columnCount = i;
							i++;
						});
						
						j = 1;
						jQuery.each(ayGrid.gridData,function(key,dtRow){
							var dt_row = jQuery('<div id = "row' + ayGrid.id + j +'"class="row' + ayGrid.id +'" />'),rw_cell;
							dt_row.data('index',j);
							i = 1;
							//dt_row.append('<span class="rowHead' + ayGrid.id +'"> </span>');
							jQuery.each(ayGrid.columnID,function(key,val){
								value = dtRow[val];
								rw_cell = jQuery('<div id = "cell' + j + ayGrid.id + i +'" class="c' + ayGrid.id + i + '" />'),
								cellType = ayGrid.columnType[val];
								jQuery(rw_cell).data('value',value);
								jQuery(rw_cell).data('index',i);
								jQuery(rw_cell).data('type',cellType);
								jQuery(rw_cell).data('columnID',val);
								switch(cellType)
								{
									case 'Label':
										rw_cell.attr('tabindex',0);
										rw_cell.attr('title',value);
										rw_cell.html(value);
										jQuery(rw_cell).keydown(function(event){if((event.keyCode == 9 || event.keyCode == 13) && event.shiftKey!=1){ayGrid.enterPress = true;this.blur();}});
										break;
									case 'Input':
										rw_cell.attr('tabindex',0);
										rw_cell.attr('title',value);
										rw_cell.html(value);
										break;
									case 'CheckBox':
										rw_cell.attr('tabindex',0);
										var cBox = jQuery('<input type="checkbox" />'),r = j-1, c = i-1,cbcell = rw_cell;
										if(value==1) cBox.attr("checked",true);//;
										rw_cell.html(cBox);
										cBox.keydown(function(event){if((event.keyCode == 9 || event.keyCode == 13) && event.shiftKey!=1){ayGrid.enterPress = true;this.blur();}});
										cBox.change(function(){
										  var oldValue,newValue = jQuery(cBox).attr("checked")==true ? 1 : 0;
										  oldValue = jQuery(cbcell).data('value');
										  jQuery(cbcell).data('value',newValue);

										  ayGrid.gridData[r][ayGrid.columnID[c]] = newValue;
										  ayCell.cell = cbcell;
										  ayGrid.onCellChange(ayCell,oldValue,newValue);
										  ayGrid.onRowDrawing(ayCell);
										});
										cBox.blur(function(event){
																	 
												ayCell.setNextCellFocus(r + 1,c + 1);
												event.stopPropagation();
																	 
																	 
										});
									  cBox.focus(function(event){
											event.stopPropagation();			   
														  });

										break;
									case 'CombBox':
										rw_cell.attr('tabindex',0);
										var tes = ayGrid.columnListData[ayGrid.columnID[i-1]][jQuery(rw_cell).data('value')];
										rw_cell.attr('title',tes);
										rw_cell.html(tes);
										jQuery(rw_cell).data('Ldata',ayGrid.columnID[i-1]);
										break;
									
								}
								
								dt_row.append(rw_cell); 
								i++;
							});

							tb_data.append(dt_row);
							ayCell.cell = rw_cell;
							ayGrid.onRowDrawing(ayCell);
							ayGrid.rowCount = j;
							j++;

						});
						
						row_str = '<div class="tools' + ayGrid.id +'"><span class="pbuttonOut' + ayGrid.id +'" id="pAdd' + ayGrid.id + '"> &nbsp;添 加&nbsp; </span><span class="pbuttonOut' + ayGrid.id +'" id="pDel' + ayGrid.id + '"> &nbsp;删 除&nbsp; </span><span class="pbuttonOut' + ayGrid.id +'" id="pEmpty' + ayGrid.id + '"> &nbsp;清  空&nbsp; </span><span class="pbuttonOut' + ayGrid.id +'" id="pSave' + ayGrid.id + '"> &nbsp;保 存&nbsp; </span></div>';
						
						row_str =row_str + '<div class="cpage' + ayGrid.id +'"><span class="pbuttonOut' + ayGrid.id +'" id="pTools' + ayGrid.id + '"> 工 具 </span><span class="pbuttonOut' + ayGrid.id +'"  id="pFirst' + ayGrid.id + '"> 首 页 </span><span class="pbuttonOut' + ayGrid.id +'"  id="pPrev' + ayGrid.id + '"> 上一页 </span><input id="pInput' + ayGrid.id + '" type="text" value=' + ayGrid.page + ' style="font-size:12px;width: 16px;border-width:0;background-color: #CCC;height: 13px;text-align: right" /><span style="background-color: #999;color: #000;height: 14px;font-size:12px">' + 'of ' + ayGrid.pages + '</span><span class="pbuttonOut' + ayGrid.id +'"  id="pNext' + ayGrid.id + '"> 下一页 </span><span class="pbuttonOut' + ayGrid.id +'"  id="pLast' + ayGrid.id + '">  尾 页  </span></div>';
						tb_pageBar.append(row_str);
						
						
						ayGrid.evensInit();
						
						jQuery('#tb_header' + ayGrid.id).css('left',-ayGrid.scrollLeft);
						jQuery('#tb_dataFrame' + ayGrid.id).scrollLeft(ayGrid.scrollLeft);
						jQuery('#tb_dataFrame' + ayGrid.id).scrollTop(ayGrid.scrollTop);
						
					},
					evensInit:function(){
						jQuery('#pTools' + ayGrid.id).unbind('click');
						jQuery('#pTools' + ayGrid.id,ayGrid.evens).click(function(){
																				  
							ayGrid.toolsDisplay =  ! ayGrid.toolsDisplay;
							jQuery('.tools' + ayGrid.id).slideToggle();
							ayGrid.setStyle();
						});
						jQuery('#pEmpty' + ayGrid.id).unbind('click');
						jQuery('#pEmpty' + ayGrid.id).click(function(){ayGrid.deleteAll()});						
						jQuery('#pDel' + ayGrid.id).unbind('click');
						jQuery('#pDel' + ayGrid.id).click(function(){ayGrid.deleteRow()});
						jQuery('#pAdd' + ayGrid.id).unbind('click');
						jQuery('#pAdd' + ayGrid.id).click(function(){ayGrid.addRow()});
						jQuery('#pSave' + ayGrid.id).unbind('click');
						jQuery('#pSave' + ayGrid.id).click(function(){ayGrid.saveGrid()});
						jQuery('#pInput' + ayGrid.id).unbind('blur');
						jQuery('#pInput' + ayGrid.id).blur(function(){ayGrid.changePage('input')});
						jQuery('#pInput' + ayGrid.id).unbind('keydown');
						jQuery('#pInput' + ayGrid.id).keydown(function(event){if((event.keyCode == 9 || event.keyCode == 13))ayGrid.changePage('input')});
						jQuery('#pFirst' + ayGrid.id).unbind('click');
						jQuery('#pFirst' + ayGrid.id).click(function(){ayGrid.changePage('first')});
						jQuery('#pPrev' + ayGrid.id).unbind('click');
						jQuery('#pPrev' + ayGrid.id).click(function(){ayGrid.changePage('prev')});
						jQuery('#pNext' + ayGrid.id).unbind('click');
						jQuery('#pNext' + ayGrid.id).click(function(){ayGrid.changePage('next')});
						jQuery('#pLast' + ayGrid.id).unbind('click');
						jQuery('#pLast' + ayGrid.id).click(function(){ayGrid.changePage('last')});
						jQuery('.pbuttonOut' + ayGrid.id).unbind('hover');
						jQuery('.pbuttonOut' + ayGrid.id).hover(function() {
							jQuery(this).addClass('pbuttonOn' + ayGrid.id);
							}, function() {
							jQuery(this).removeClass('pbuttonOn' + ayGrid.id);
						});
						jQuery('#tb_dataFrame' + ayGrid.id).unbind('scroll');
						jQuery('#tb_dataFrame' + ayGrid.id).scroll(function() {
							
							jQuery('#tb_header' + ayGrid.id).css('left',-this.scrollLeft);
							//jQuery('#tb_headerFrame' + ayGrid.id).scrollLeft(this.scrollLeft);
							ayGrid.scrollLeft = this.scrollLeft;
							ayGrid.scrollTop  = this.scrollTop;
							//alert(this.scrollTop);
							
						});

						jQuery('.row' + ayGrid.id).unbind('hover');
						jQuery('.row' + ayGrid.id,ayGrid.evens).hover(function() {
							
							jQuery('div',jQuery(this)).addClass('rowover' + ayGrid.id);
							}, function() {
							jQuery('div',jQuery(this)).removeClass('rowover' + ayGrid.id);
							
						});
						jQuery('.row' + ayGrid.id + ' div').unbind('hover');
						jQuery('.row' + ayGrid.id + ' div',ayGrid.evens).hover(function() {
							
							jQuery(this).addClass('cellselected' + ayGrid.id);
							}, function() {
							jQuery(this).removeClass('cellselected' + ayGrid.id);
							
						});
						jQuery('.row' + ayGrid.id).unbind('click');
						jQuery('.row' + ayGrid.id,ayGrid.evens).click(function() {
							if(ayGrid.ctrlKeyDown == false){
								jQuery('.rowselected' + ayGrid.id).removeClass('rowselected' + ayGrid.id);
								jQuery('.rowofselected' + ayGrid.id).removeClass('rowofselected' + ayGrid.id);
								}
							ayGrid.activeRow = jQuery(this);
							jQuery(ayGrid.activeRow).addClass('rowofselected' + ayGrid.id);
							jQuery('div',ayGrid.activeRow).addClass('rowselected' + ayGrid.id); 
						});	
						jQuery('.column' + ayGrid.id + ' div').unbind('mousedown');
						jQuery('.column' + ayGrid.id + ' div',ayGrid.evens).mousedown(function(){
																		 
								if(ayGrid.columnMouseIn==true){
									ayGrid.columnMouseDown = true;
									ayGrid.columnWidth = $(this).width();
									ayGrid.dataWidth = jQuery('#tb_header' + ayGrid.id).width();
									}
									
																		 
						});
						jQuery('#aytable' + ayGrid.id).unbind('mouseup');
						jQuery('#aytable' + ayGrid.id).mouseup(function(){
																		 
								
							if(ayGrid.columnMouseDown == true){	
								
								ayGrid.columnMouseDown = false;										 
								ayGrid.setStyle();										 
								$(this).css({'cursor': 'default'});	
								
							}
																		 
						});	
						jQuery('.column' + ayGrid.id + ' div').unbind('mouseout');
						jQuery('.column' + ayGrid.id + ' div',ayGrid.evens).mouseout(function(){
																		 
								ayGrid.columnMouseIn = 	false;									 
																		 
																		 
																		 
						});
						jQuery('#aytable' + ayGrid.id).unbind('mousemove');
						jQuery('#aytable' + ayGrid.id).mousemove(function(e){								 
																		 
							if(ayGrid.columnMouseDown == true){
								
								var offsetX = e.pageX - ayGrid.columnMouseDX;
								var offsetW = ((ayGrid.columnWidth + offsetX) > 0 ? offsetX : (1-ayGrid.columnWidth));
								
								jQuery('#tb_header' + ayGrid.id).width(ayGrid.dataWidth + offsetW) ;
								jQuery('#tb_data' + ayGrid.id).width(ayGrid.dataWidth + offsetW) ;
								jQuery('.' + ayGrid.columnCss,ayGrid.evens).width(ayGrid.columnWidth + offsetW);
								jQuery('#tb_header' + ayGrid.id).css('left',-jQuery('#tb_dataFrame' + ayGrid.id).scrollLeft());
								jQuery('#' + ayGrid.id).data(ayGrid.columnCur + '_',ayGrid.columnWidth + offsetW);

								$(this).css({'cursor': 'e-resize'});
							}																		 
																		 
						});	
						jQuery('.column' + ayGrid.id + ' div div').unbind('mousedown');
						jQuery('.column' + ayGrid.id + ' div div',ayGrid.evens).mousedown(function(){

							if(ayGrid.columnMouseDown == false && ayGrid.columnMouseIn==false){
								var typesort = ayGrid.columnSort == 'desc' ?  'desc' :ayGrid.columnSort;												 
								ayGrid.sortColumn(typesort,$(this).parents('div').data('column'));
								
								ayGrid.columnSort = typesort == 'asc' ? 'desc' : 'asc';
								
							}
							
											
																			 
						});
						jQuery('.column' + ayGrid.id + ' div div').unbind('mousemove');
						jQuery('.column' + ayGrid.id + ' div div',ayGrid.evens).mousemove(function(e){
							var offsetX = 0;
							
							if((e.pageX > ($(this).offset().left + $(this).width() - 5)) && (ayGrid.columnMouseDown == false)){
								ayGrid.columnMouseIn = true;
								ayGrid.columnMouseDX = e.pageX;
								ayGrid.columnCss = $(this).parents('div').attr('class');
								ayGrid.columnCur = $(this).parents('div').data('column');

								//alert(ayGrid.columnCss);
								$(this).css({'cursor': 'e-resize'});
							}							
							else{
								if(ayGrid.columnMouseDown == false)$(this).css({'cursor': 'default'});
								ayGrid.columnMouseIn = 	false;
							}

					    });
						jQuery('.row' + ayGrid.id + ' div').unbind('focus');
						jQuery('.row' + ayGrid.id + ' div',ayGrid.evens).focus(function(event){
							ayCell.cell = this;
							var cell = jQuery(this);
							var cellType = jQuery(cell).data('type');
							if(cellType !='Label' && cellType !='CheckBox'){
								this.blur();
								
							}
							
							event.stopPropagation();
						});
						jQuery('.row' + ayGrid.id + ' div').unbind('keydown');
						jQuery('.row' + ayGrid.id + ' div').keydown(function(event){

							var cell = jQuery(this);
							var cellType = jQuery(cell).data('type');
							//alert(event.keyCode);
							if(event.keyCode == 17)ayGrid.ctrlKeyDown = true;
							if((event.keyCode == 9 || event.keyCode == 13) && event.shiftKey!=1 && cellType =='Label'){
								
								ayGrid.enterPress = true;
								this.blur();
								
								}
							
							event.stopPropagation();
							});
						jQuery('.row' + ayGrid.id + ' div').unbind('keyup');
						jQuery('.row' + ayGrid.id + ' div').keyup(function(event){
																			 
								if(event.keyCode == 17)ayGrid.ctrlKeyDown = false;;											 
																			 
																			 
							});
						jQuery('.row' + ayGrid.id + ' div').unbind('click');
						jQuery('.row' + ayGrid.id + ' div',ayGrid.evens).click(function(event){
						   ayCell.cell = this;						   
						   ayGrid.CombBoxFocusFix = true;
						   ayGrid.cellClick(ayCell);
																					   
							});
						jQuery('.row' + ayGrid.id + ' div').unbind('blur');
						jQuery('.row' + ayGrid.id + ' div',ayGrid.evens).blur(function(event){
						   var cell = jQuery(this),r = ayCell.rowIndex(),c = ayCell.columnIndex();
						   var cellType = jQuery(cell).data('type'),t = cell.attr('tabindex');
						  
							switch(cellType)
							{
								case 'Label':
									
									ayCell.setNextCellFocus(r,c);
								break;
								case 'Input':
									
									var oldValue = $(this).text();   
									  var input = jQuery("<input type='text' value='" + oldValue + "' style='font-size:12px' />");
									  input.width(cell.width());
									  input.height(cell.height());
									  input.click(function() {
									  return false;
									  });
									  input.css({ "border-width": "0"});
									  cell.html(input);
										 input.keydown(function(event){if((event.keyCode == 9 || event.keyCode == 13) && event.shiftKey!=1){ayGrid.enterPress = true;this.blur();event.stopPropagation();}});
										 input.blur(function(event) {
												
													var newValue = input.attr("value");
													cell.html(newValue); 
													ayGrid.gridData[r-1][ayGrid.columnID[c-1]] = input.attr('value');
													jQuery(cell).data('value',input.attr("value"));
													ayCell.cell = cell;
													if(oldValue != newValue){
														ayGrid.onCellChange(ayCell,oldValue,newValue);
														ayGrid.onRowDrawing(ayCell);
													}
													ayCell.setNextCellFocus(r,c);
													event.stopPropagation();
										 });
									  input.focus(function(event){
											event.stopPropagation();			   
														  });

									  input.focus(); 
								break;
								case 'CheckBox':
								
									cell.find("input").focus();

								break;
								case 'CombBox':
								
									var oldValue = jQuery(cell).data('value'),coID = jQuery(cell).data('Ldata');
									ayGrid.CombBoxFocusFix = false;
									
									  var input = jQuery("<select />");
									  input.width(cell.width());
									  input.height(cell.height());
									  
									  input.append('<option value=""></option>');
									  jQuery.each(ayGrid.columnListData[coID],function(key,dtRow){
													input.append('<option value="' + key +'">' + dtRow +'</option>');					   
																		   });

									  input.attr("value",oldValue);
									  input.click(function() {
									  return false;
									  });
									  input.css({ "border-width": "0" ,"font-size":"12px"});
									  cell.html(input);
										 input.keydown(function(event){
																if((event.keyCode == 9 || event.keyCode == 13) && event.shiftKey!=1){
																		  ayGrid.CombBoxFocusFix = true;
																		  ayGrid.enterPress = true;
																		  this.blur();
																		  }
											});
										 input.blur(function(event) {
													var newValue = input.attr('value');
													cell.data('value',newValue);
													ayGrid.gridData[r-1][ayGrid.columnID[c-1]] = input.attr('value');
													cell.html(input.find("option:selected").text());
													ayCell.cell = cell;
													if(oldValue != newValue){
														ayGrid.onCellChange(ayCell,oldValue,newValue);
														ayGrid.onRowDrawing(ayCell);
													}
													ayCell.setNextCellFocus(r,c);
													event.stopPropagation();
													if(ayGrid.enterPress == false && ayGrid.CombBoxFocusFix == false ){//&& ayGrid.CombBoxClickYN == false
														cell.focus();
														ayGrid.CombBoxFocusFix = true;
														}
													
										 });
									  input.focus(function(event){

											event.stopPropagation();			   
														  });
									
									 input.focus();
									 input.attr("selectedIndex ",1); 

									break;

							}

							event.stopPropagation();
						});						
						
						},
					changePage: function (ctype){ //change page
			
							if (this.loading) return true;
						
							switch(ctype)
							{
								case 'first': ayGrid.page = 1; break;
								case 'prev': if (ayGrid.page>1) ayGrid.page = parseInt(ayGrid.page) - 1; break;
								case 'next': if (ayGrid.page<ayGrid.pages) ayGrid.page = parseInt(ayGrid.page) + 1; break;
								case 'last': ayGrid.page = ayGrid.pages; break;
								case 'input': 
										var nv = parseInt(jQuery('#pInput' + ayGrid.id).val());
										if (isNaN(nv)) nv = 1;
										if (nv<1) nv = 1;
										else if (nv > ayGrid.pages) nv = ayGrid.pages;
										ayGrid.page = nv;
										break;
							}
							ayGrid.getGridData();
					
					},
					deleteAll:function(){
						
									ayGrid.gridData = [];
									ayGrid.activeRow = null;
									ayGrid.rowCount = 0;
									ayGrid.createGrid();
						},
					deleteRow:function(){

						var selectedIndex = [],df = jQuery('.rowofselected' + ayGrid.id),i = 0,rkey;
						
						df.each(function(){
							selectedIndex[i] = jQuery(this).data('index')-1;
							i = i + 1;
							});
						selectedIndex.sort(function(a,b){return a<b?1:a==b?0:-1});
						jQuery.each(selectedIndex,function(){
							rkey = ayGrid.gridData[this][ayGrid.rowKey];
							ayGrid.rowDelete(rkey);
							ayGrid.gridData.splice(this,1);
							ayGrid.rowCount = ayGrid.rowCount - 1;
							});
						ayGrid.activeRow = null;
						ayGrid.createGrid();
						},
					addRow:function(){       
						var adrow = {},j = ayGrid.rowCount ;
						jQuery.each(ayGrid.columnID,function(){
															 
								adrow[this] = '';							   
						});
						ayGrid.gridData[j] = adrow;
						ayGrid.rowCount = j+1;
						ayGrid.createGrid();
						
						jQuery('#tb_header' + ayGrid.id).css('left',0);
						jQuery('#tb_dataFrame' + ayGrid.id).scrollLeft(0);
						jQuery('#tb_dataFrame' + ayGrid.id).scrollTop(jQuery('#tb_dataFrame' + ayGrid.id).attr('scrollHeight'));
						
						},
					saveGrid:function(){        
						
						var reflash = ayGrid.gridSave(ayGrid.gridData);
						if(reflash != false)ayGrid.getGridData();
						}
					};//end aygrid class
					
				var ayCell ={
					cell:null,
					gridID:ayGrid.id,
					columnListData:ayGrid.columnListData,
					rowCount:0,
					columnCount:0,
					setNextCellFocus:function (r,c){
						var nextRow = ayGrid.columnCount * r,
						firstCell = ayGrid.columnCount * ayGrid.rowCount,
						changeValue = r * c,
						nr = 1 ,nc = 1;
						switch(changeValue)
						{
							case  firstCell:
								//nr = 1,nc =1;
							break;
							case nextRow:
								
								nr = r +1;
								//nc = 1 ;
							break;
							default:
							
								nr = r,nc = c+1;
							break;
							
							}
						if(ayGrid.enterPress){
							jQuery('#cell' + nr + ayCell.gridID + nc).focus();
							jQuery('#tb_dataFrame' + ayGrid.id).trigger('scroll');
							ayGrid.enterPress = false;
						}
						},
					rowIndex:function(){
						
						return jQuery(ayCell.cell).parents('div').data('index');
						},
					columnIndex:function(){
						
						return jQuery(ayCell.cell).data('index');
						},
					setCss:function(r,c,css){
						jQuery('#cell' + r + ayCell.gridID + c).css(css);
						},
					getRowKey:function(r){
						Value = ayGrid.gridData[r-1][ayGrid.rowKey];
						return Value;
						},
					getColumnID:function(){
						Value = jQuery(ayCell.cell).data('columnID');;
						return Value;
						},
					setValue:function(r,c,Value){
						var oldValue = ayGrid.gridData[r-1][ayGrid.columnID[c-1]];
						var newValue = Value;
						
						ayGrid.gridData[r-1][ayGrid.columnID[c-1]] = Value;

						var ce = jQuery('#cell' + r + ayCell.gridID + c);
						var cellType = ce.data('type');
						jQuery(ce).data('value',Value);
							switch(cellType)
							{
								case 'Label': 
									 
									jQuery(ce).html(Value);
									
								break;
								case 'Input':  

									jQuery(ce).html(Value);
									
								break;
								case 'CheckBox':
								
									jQuery(ce).children('input').attr("checked",Value == 1 ? true :false);							
										
								break;
								case 'CombBox':
										var tes = ayCell.columnListData[jQuery(ce).data('Ldata')][Value];
										jQuery(ce).html(tes);
								
								break;
							};
						ayCell.cell = ce;
						ayGrid.onCellChange(ayCell,oldValue,newValue);
						},
						
					getValue:function(r,c){
						
						return jQuery('#cell' + r + ayCell.gridID + c).data('value');
						},
					getValueByCid:function(r,Cid){
						
						return ayGrid.gridData[r-1][Cid];
						}
					
					};
				ayGridFn = {
					changePage:function(ctype){
						ayGrid.changePage(ctype);
						},
					loadData:function(dataJsonUrl){
						ayGrid.page = 1;
						ayGrid.dataJsonUrl = dataJsonUrl;
						ayGrid.getGridData();
						},
					deleteRow:function(){
						ayGrid.deleteRow();
						
						},
					deleteAll:function(){
						ayGrid.deleteAll();
						
						},
					addData:function(rowData){
						var j = ayGrid.rowCount ;
						ayGrid.gridData[j] = rowData;
						ayGrid.rowCount = j+1;
						ayGrid.createGrid();
						
						jQuery('#tb_header' + ayGrid.id).css('left',0);
						jQuery('#tb_dataFrame' + ayGrid.id).scrollLeft(0);
						jQuery('#tb_dataFrame' + ayGrid.id).scrollTop(jQuery('#tb_dataFrame' + ayGrid.id).attr('scrollHeight'));
						},
					setValue:function(r,c,Value){
						ayCell.setValue(r,c,Value);
						},
					saveNewData:function(){
						ayGrid.saveGrid();
						
						}
					
					};
					
				ayGrid.setStyle();
				ayGrid.gridData = settings.dataJson;
				
				if(settings.dataJsonUrl!=''){
					ayGrid.dataJsonUrl = settings.dataJsonUrl;
					ayGrid.getGridData();
					}
				else{
					ayGrid.createGrid();
					}
				
				
				

			});
		
		return ayGridFn;}
	})
})(jQuery);