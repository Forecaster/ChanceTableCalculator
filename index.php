<?

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Chance Table Calculator</title>
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
	<style>
	body
	{
		background-color: black;
		color: white;
	}
	
	.form-control, .form-control[readonly], .ui-selectmenu-button
	{
		background-color: #1d1d1d;
		color: white;
	}
	
	.ui-selectmenu-button.ui-button
	{
		width: 100%;
	}
	
	.header
	{
		background-color: gray;
		color: white;
	}
	
	.error
	{
		color: red;
		font-weight: bold;
		font-size: 14pt;
	}

		.collapse-container {}

		.collapse-container > div:nth-child(1) {
			display: none;
		}

		.collapse-control {
			margin-top: 10px;
			cursor: pointer;
		}
	</style>
</head>
<body>
<div id="row_template_container" style="display: none;">
	<div class="row" id="row_template" style="margin-top: 10px;">
		<div class="col-md-4">
			<input class="form-control" type="text" placeholder="Name"/>
		</div>
		<div class="col-md-1">
			<input class="form-control percentage_mode_plc" type="text" placeholder="Wgt"/>
		</div>
		<div class="col-md-2">
			<input class="form-control" type="text" placeholder="Output" readonly="readonly"/>
		</div>
		<div class="col-md-1" style="line-height: 25pt">
			<span>?</span>
		</div>
		<div class="col-md-1">
			<div class="btn btn-danger" onclick="btn_delete_row_confirm(event)">Delete row</div>
		</div>
	</div>
</div>
<div id="group_template_container" style="display: none;">
	<div id="group_template" class="group">
		<div id="row_container">

		</div>
		<div class="row" style="margin-top: 10px;">
			<div class="col-md-2">
				<div class="btn btn-primary" onclick="btn_add_row(event)">Add row</div>
			</div>
			<div class="col-md-1">
				<div class="btn btn-danger" onclick="btn_delete_group_confirm(event)">Delete group</div>
			</div>
		</div>
	</div>
</div>
<div class="container" style="margin-top: 20px">
	<div class="collapse-container">
		<div>
			<div class="row" style="margin-top: 20px;">
				<div class="col-md-12">
					<textarea class="form-control" id="io" placeholder="Save & load"></textarea>
				</div>
			</div>
			<div class="row" style="margin-top: 10px;">
				<div class="col-md-1">
					<div class="btn btn-primary" onclick="load();">Load</div>
				</div>
				<div class="col-md-1">
					<div class="btn btn-success" onclick="save();">Save</div>
				</div>
			</div>
			<div class="row" style="margin-top: 10px;">
				<div class="col-md-12">
					<b>Saving:</b> Click save and copy output and save to a textfile or similar.<br/>
					<b>Loading:</b> Copy previously saved output, paste into textbox and click load.
				</div>
			</div>
		</div>
		<div class="collapse-control" onclick="toggle_collapse(event);">
			Show loading & saving
		</div>
	</div>
	<div class="collapse-container" style="margin-top: 10px;">
		<div>
			<div class="row">
				<div class="col-md-2">
					<label for="option_dice_size">Dice size</label>
				</div>
				<div class="col-md-2">
					<select id="option_dice_size" class="form-control">
						<option value="dynamic">Dynamic</option>
						<option value="4">d4</option>
						<option value="6">d6</option>
						<option value="8">d8</option>
						<option value="10">d10</option>
						<option value="12">d12</option>
						<option value="20">d20</option>
						<option value="100">d100</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-2">
					<label for="option_dice_num">Number of dice</label>
				</div>
				<div class="col-md-2">
					<input type="number" class="form-control" id="option_dice_num" min="0" max="100" placeholder="1" onclick="this.select();" />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<label><input type="checkbox" data-toggle="toggle" id="option_strict_dice" checked="checked" /> Strict dice</label>
				</div>
			</div>
		</div>
		<div class="collapse-control" onclick="toggle_collapse(event);">
			Show settings
		</div>
	</div>
	<div class="row" style="margin-top: 10px;">
		<div class="col-md-12 error" id="error_output">
		</div>
	</div>
	<div id="group_container">
	</div>
	<div class="row" style="margin-top: 20px;">
		<div class="col-md-2">
			<div class="btn btn-primary" onclick="btn_add_group()">Add group</div>
		</div>
		<div class="col-md-1">
			<div class="btn btn-success" onclick="update()">Update</div>
		</div>
	</div>
	<div class="row" style="margin-top: 10px;">
		<div class="col-md-12">
			<b>Name:</b> Only used for labeling rows. Optional.<br/>
			<b>Weight:</b> An integer denoting the row weight, for example <code>1</code> or <code>100</code>.<br/>
			<b>Output:</b> Readonly output field containing the dice range.
		</div>
	</div>
</div>
</body>
<script>
	const option_dice_size = document.getElementById("option_dice_size");
	const option_dice_num = document.getElementById("option_dice_num");
	const option_strict_dice = document.getElementById("option_strict_dice");

	const group_container = document.getElementById("group_container");
	const row_template = document.getElementById("row_template");
	const group_template = document.getElementById("group_template");
	const io = document.getElementById("io");
	const error_output = document.getElementById("error_output");

	//<editor-fold desc="Settings functions">

	function get_dice_size() {
		return option_dice_size.value;
	}

	function get_dice_num() {
		let num = parseInt(option_dice_num.value);
		if (isNaN(num))
			return 1;
		return num;
	}

	function get_strict_dice() {
		return option_strict_dice.checked;
	}

	/**
	 * @param total_weight number
	 * @returns {{min: number, max: number}}
	 */
	function get_dynamic_dice_size(total_weight) {
		let min = 0;
		let max = 0;
		if (get_dice_size() === "dynamic") {
			if (get_strict_dice()) {
				if (total_weight <= 4) {
					min = 1; max = 4;
				} else if (total_weight <= 6) {
					min = 1; max = 6;
				} else if (total_weight <= 8) {
					min = 1; max = 8;
				} else if (total_weight <= 10) {
					min = 1; max = 10;
				} else if (total_weight <= 12) {
					min = 1; max = 12;
				} else if (total_weight <= 20) {
					min = 1; max = 20;
				} else if (total_weight <= 100) {
					min = 1; max = 100;
				} else {
					min = 1; max = -1;
				}
			} else {
				min = 1; max = total_weight;
			}
		} else {
			min = parseInt(get_dice_num());
			max = parseInt(get_dice_num()) * parseInt(get_dice_size());
		}
		return { min: min, max: max };
	}

	//</editor-fold>

	/**
	 * @params amount number
	 * @returns Element[]
	 */
	function add_group(amount) {
		if (typeof amount === "undefined")
			amount = 1;
		let groups = [];
		for (let i = 0; i < amount; i++) {
			let div = document.createElement("div");
			div.className = group_template.className;
			div.style.cssText = group_template.style.cssText;
			div.innerHTML = group_template.innerHTML;
			group_container.appendChild(div);
			groups.push(div);
		}
		return groups;
	}

	/**
	 * Adds [amount] rows to list and returns list of added row elements
	 * @param amount number
	 * @param group_row_container Element
	 * @returns Element[]
	 */
	function add_row(amount, group_row_container) {
		if (typeof amount == "undefined")
			amount = 1;
		let rows = [];
		let div = null;
		for (let i = 0; i < amount; i++) {
			div  = document.createElement("div");
			div.className = row_template.className;
			div.style.cssText = row_template.style.cssText;
			div.innerHTML = row_template.innerHTML;
			group_row_container.appendChild(div);
			rows.push(div);
		}
		return rows;
	}

	function btn_add_row(event) {
		console.info(event.target);
		const group_row_container = get_group_row_container(event.target.parentElement.parentElement.parentElement);
		add_row(1, group_row_container);
	}

	function btn_add_group() {
		let groups = add_group(1);
		add_row(2, get_group_row_container(groups[0]));
	}

	function btn_delete_group_confirm(event) {
		let button = event.target;
		button.innerText = "Really?";
		button.onclick = btn_delete_group;

		setTimeout(function() {
			button.innerText = "Delete group";
			button.onclick = btn_delete_group_confirm;
		}, 5000);
	}

	function btn_delete_group(event) {
		let group = event.target.parentElement.parentElement.parentElement;
		group.parentElement.removeChild(group);
	}

	function btn_delete_row_confirm(event) {
		let button = event.target;
		button.innerText = "Sure?";
		button.onclick = btn_delete_row;

		setTimeout(function() {
			button.innerText = "Delete row";
			button.onclick = btn_delete_row_confirm;
		}, 5000);
	}
	
	function btn_delete_row(event) {
		let row = event.target.parentElement.parentElement;
		row.parentElement.removeChild(row);
	}
	
	function get_name(row) {
		//console.log(row);
		return row.children[0].children[0];
	}
	
	function get_input(row) {
		//console.log(row);
		return row.children[1].children[0];
	}
	
	function get_output(row) {
		//console.log(row);
		return row.children[2].children[0];
	}

	function get_percentage(row) {
		//console.log(row);
		return row.children[3].children[0];
	}

	function get_group_row_container(group) {
		return group.children[0];
	}

	function toggle_collapse(event) {
		let container = event.target.parentElement.children[0];
		let control = event.target.parentElement.children[1];

		if (control.innerText.indexOf("Show") !== -1) {
			$(container).show(500);
			control.innerText = control.innerText.replace("Show", "Hide");
		} else {
			$(container).hide(500);
			control.innerText = control.innerText.replace("Hide", "Show");
		}
	}

	/**
	 * Range calculation function
	 */
	function update() {
		let groups = group_container.children;


		for (let i = 0; i < groups.length; i++) {
			let input;
			let children = groups[i].children[0].children;
			let values = [];
			let total_weight = 0;

			for (let i = 0; i < children.length; i++) {
				input = get_input(children[i]);
				total_weight += parseFloat(input.value);
			}

			console.info("Total weight: " + total_weight);

			for (let i = 0; i < children.length; i++) {
				input = get_input(children[i]);
				let weight = NaN;
				weight = parseInt(input.value);

				const percentage = (weight / total_weight) * 100;
				console.info("Row #" + i + " takes up " + percentage + "% of total weight");
				values.push({
					row: children[i],
					weight: weight,
					percentage: percentage
				});
			}

			console.log(values);
			const dice = get_dynamic_dice_size(total_weight);
			console.log(dice);

			let position = dice.min;
			for (let i = 0; i < values.length; i++) {
				console.info("Row " + i);
				let value = values[i];
				if (isNaN(value.weight)) {
					get_output(value.row).value = "Invalid";
				} else {
					const percentage = value.weight / total_weight;
					console.log("Percentage: " + percentage);

					const size = Math.max(1, Math.floor(percentage * dice.max));
					console.log("Size: " + size);

					let range;
					if (size === 1) {
						range = position;
						console.log("Start: " + position);
					} else {
						let start = position;
						let end = Math.min(position + (size - (values.length === i ? 0 : 1)), dice.max);
						console.log("Start: " + start + ", end: " + end);
						range = start + "-" + end;
					}

					get_output(value.row).value = range;
					get_percentage(value.row).innerText = parseFloat((percentage * 100).toFixed(2)) + "%";
					position += size;
				}
			}
		}
	}
	
	function save() {
		const groups = group_container.children;
		let data = [];

		for (let i = 0; i < groups.length; i++) {
			const children = groups[i].children[0].children;
			let rows = [];
			for (let i = 0; i < children.length; i++) {

				rows.push({
					name: get_name(children[i]).value,
					input: get_input(children[i]).value,
					output: get_output(children[i]).value,
					percentage: get_percentage(children[i]).innerText
				});
			}
			data.push(rows);
		}
		
		io.value = JSON.stringify(data);
	}
	
	function load() {
		try {
			const groups = JSON.parse(io.value);
			group_container.innerHTML = "";
			
			console.log(groups);
			for (let i = 0; i < groups.length; i++) {
				const group = add_group(1)[0];
				const rows = groups[i];

				for (let i = 0; i < rows.length; i++) {
					console.log("Add row " + rows[i].name);
					const row = add_row(1, get_group_row_container(group))[0];
					get_name(row).value = rows[i].name;
					get_input(row).value = rows[i].input;
					get_output(row).value = rows[i].output;
					get_percentage(row).innerText = rows[i].percentage;
				}
			}
		} catch (ex) {
			if (ex.message.toLowerCase().indexOf("unexpected") !== -1)
				error_output.innerText = "No data to load or invalid JSON. Check console for more information.";
			else
				error_output.innerText = ex.message;
			console.error(ex);
		}
	}

	let groups = add_group(1);
	add_row(2, groups[0].children[0]);
</script>