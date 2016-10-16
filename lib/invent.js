//$(document).ready(function() {
	set_events();
	recalc();
	
	$('button.add_to_order').on('click', function(){
		var tr = $(this).parents('tr');
		//var id = $(tr).attr('data-key');
		var id = $(tr).find("td").eq(2).find("input.t_id").val()
		var name = $(tr).find("td").eq(2).text();
		var price = $(tr).find("td").eq(3).text();
		var sklad_id = $(tr).find("td").eq(6).find("input.sklad_id").val();
		var sklad_name = $(tr).find("td").eq(6).text();

		add_price_row(id,name,sklad_id,sklad_name,price);
	});

	function add_price_row(id,name,sklad_id,sklad_name,price) {
		var idf = new Date();
		var doc = window.parent.document;
		var lr = $(doc).find("#last_row");	
		var len = $(doc).find('tr.tovar-row').length;
		len = len+1;
		var amount = 1;
		var sum = price*amount;	
		var s = '<tr class="tovar-row">';		
			s+='<td class="num">'+len+'</td>';
			s+='<td class="name">'+name+'</td>';
			s+='<td class="sklad_id">'+sklad_name+'<input type="hidden" name="tovar_list[new]['+idf.getTime()+'][sklad_id]" class="sklad_id" value="'+sklad_id+'" /></td>';
			s+='<td class="price">'+price+'</td>';
			s+='<td class="amount"><input type="text" name = "tovar_list[new]['+idf.getTime()+'][amount]" class="form-control amount" value="'+amount+'" /></td>';		
			s+='<td class="sum">'+sum+'</td>';
			s+='<td><input type="hidden" name="tovar_list[new]['+idf.getTime()+'][id]" class="id" value="'+id+'" /><button type="button" class="btn btn-default btn-sm" aria-label="Удалить"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button></td>';
			s+='</tr>';
		 var new_row = $(s);
		 lr.before(new_row);
		 recalc(doc);
		 //window.close()
		 //set_events();
	}

	function delete_row() {			
		//var len = $('tr.tovar-row').length;	
		//if (len<=1) {alert('Должен быть хотя бы один товар');return false;}
		$(this).parents('.tovar-row').remove();
		recalc();
	}
	function check_values(tr) {
		var sum_input = $("input.sum", tr);
		var qnt_input = $("input.qnt", tr);
		var cst_input = $("input.cst", tr);
		var unt_input = $("input.unt", tr);
		var nam_input = $("input.nam", tr);

		var qnt = parseFloat(qnt_input.val()).toFixed(0);
		var cst = parseFloat(cst_input.val()).toFixed(2);
		var sum = parseFloat(sum_input.val()).toFixed(2);

		if (isNaN(qnt)) qnt_input.val("")
		else qnt_input.val(qnt);

		if (isNaN(cst)) cst_input.val("")
		else cst_input.val(cst);

		if (isNaN(sum)) sum_input.val("")
		else sum_input.val(sum);

		if (unt_input.val()=='') {alert('Укажите Единицу измерения'); unt_input.val('шт.');}

		if (nam_input.val()=='') {alert('Укажите Наименование'); nam_input.val('Наименование');}
	}

	function on_qnt_change() {
		var tr = $(this).parents("tr").get(0);

		var sum_input = $("input.sum", tr);
		var qnt_input = $("input.qnt", tr);
		var cst_input = $("input.cst", tr);

		var qnt = parseFloat(qnt_input.val());
		var cst = parseFloat(cst_input.val());

		if ((!isNaN(qnt))||(!isNaN(cst))) sum_input.val(qnt*cst);

		check_values(tr);
		count_totals();
	}

	function count_totals() {
		var totals = 0;

		$("tr.row", $("#table_works")).each(
		function()
		{
		  var row_sum = parseFloat($("input.sum", this).val());
		  if (!isNaN(row_sum)) totals = totals + row_sum;
		}
	);

	$("#total").val(totals.toFixed(2))
}

	function recalc(doc) {
		if (typeof doc =='undefined') var doc = $('#tovar-list');
		
		renum(doc);
	
		var totals = 0;
		var totals_qnt = 0;

		$("tr.tovar-row", doc).each(//	$("tr.tovar-row", $("#tovar-list")).each(
			function()
			{
			  //var tr = $(this).parents("tr").get(0);
	
			  var sum_input = $("td.sum", this);
			  var qnt_input = $("td.amount input", this);
			  var cst_input = $("td.price", this);

			  var qnt = parseInt(qnt_input.val());
			  var cst = parseFloat(cst_input.html());

			  if ((!isNaN(qnt))||(!isNaN(cst))) sum_input.html((qnt*cst).toFixed(2));

			  var row_sum = parseFloat($("td.sum", this).html());
			  if (!isNaN(row_sum)) totals = totals + row_sum;
			  if (!isNaN(qnt)) totals_qnt = totals_qnt + qnt;
			 
			}
		);
		var discount = parseFloat($('input#orders-discount', doc).val());
		
		if (!isNaN(discount) && discount <= totals) totals = totals - discount;

		$("#total_sum", doc).text(totals.toFixed(2))
		$("#total_qnt", doc).text(totals_qnt.toFixed(0))
	}

	function renum(doc) {
		var totals = 0;	
		$('td.num', doc).each(
		function() {
			totals++;		
			$(this).text(totals);
		})
	}
	function set_events() {		
		//$("input.amount").blur(function() {alert('111');recalc();});
		$('#tovar-list').on('click', 'button', delete_row);
		$('#tovar-list').on('blur', 'input.amount', function(){recalc()});
		$('#tovar-list').on('blur', 'input#orders-discount', function(){recalc()});		
	}

	$(".various").fancybox({
		padding: 5,
		maxWidth	: 960,
		maxHeight	: 600,
		//fitToView	: false,
		width		: '100%',
		height		: '100%',		
		openEffect	: 'none',
		closeEffect	: 'none',
		scrollOutside: false
	});



		


	
	//$('a#add').on('click', openWin);//$('a#add').on('click', add_row);
	//$('a#add').on('click', add_row);
//});