/*******************************************************************************
editResource.js

Description: JavaScript for editing resources page.

Created by Karl Doerr, 
Modified by Troy Hurteau, Eric McEachern,
NCSU Libraries, NC State University (libraries.opensource@ncsu.edu).

Copyright (c) 2011 North Carolina State University, Raleigh, NC.

EZadmin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

EZadmin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

EZadmin as distributed by NCSU Libraries is located at:
http://code.google.com/p/ezadmin/

*******************************************************************************/


function toggleConfigDisplay()
{
	if($("#toggleConfig").hasClass('urlOption')){
		$("#toggleConfig").removeClass('urlOption');
	} else {
		$("#toggleConfig").addClass('urlOption');
	}
	updateConfigDisplay();
}

function updateConfigDisplay()
{
	if($("#toggleConfig").hasClass('urlOption')){
		$("input[name=use_custom]").val('false');
		$("#toggleConfig").html('use custom configuration');
		$("#configUrls").show();
		$("#configCustom").hide();
	} else {
		$("input[name=use_custom]").val('true');
		$("#toggleConfig").html('use standard URLs');
		$("#configCustom").show();
		$("#configUrls").hide();
	}	
}

function activateDeletePrompt(){
	var par = $(this).parent();
	var myUrl = $('.urlInput', par).val();
	var myType = $('option[value="' + $('select', par).val() + '"]',par).html();
	var remv = confirm(
	    'Are you sure you want to delete that configuration option?\n\n'
	    + 'URL: ' + myUrl + '\nURL Type: ' + myType
	);
	if(remv == true){
		par.remove();
	}
}

    $(document).ready(function(){

		var x= document.getElementById("created_urls").value;
		$("#customConfig").hide();
		updateFormState();
	
		$("#toggleConfig").live('click keypress', toggleConfigDisplay);	
	
		$("#addUrl").live('click keypress', function (){
			var configTypes = getConfigTypes();
			var configOptions = '';
			for(type in configTypes){
					configOptions += '<option value="' + configTypes[type] + '">' + type + '</option>';
			}
			$("#configUrls .floatClear").before(
					'<div class="urlConfigBlock">'
					+ '<a class="deleteUrlButton" href="#" onClick="return false;" id="delete_' + x + '" class="del">delete URL</a>'
					+ '<label for="url_name_' + x + '">URL:</label><input type="text" value="" size="50" name="url_name_' + x + '" id="url_name_' + x + '" />'
					+ '<label for="url_select_' + x + '">URL Type:</label><select name="url_select_' + x + '">'
					+ configOptions
					+ '</select>'
					+ '</div>'
		    );
			$("#delete_" + x).click(activateDeletePrompt)
			x++;
			$("#created_urls").remove();
			$("#resource_form").append("<input type='hidden' name='created_urls' value='"+x+"' id='created_urls' />")
			
		});
		$(".deleteUrlButton").click(activateDeletePrompt);
		
		$("#delete_resource").click(function (){
			var delres = confirm("Are you sure you want to delete the whole resource?");
			if(delres == true){
				$("#resource_form").append("<input type = 'hidden' name='delete_resource' value='true' />");
				$('#resource_form').submit();
			}	
		})
		$("#cancel").click(function (){
			window.location = 'index.php';	
		})

	});

function testing(){
	var resourceName = document.getElementById('resource_name').value;
	if(resourceName == "" || resourceName == null){
		alert("The Resource name is blank!");
		return false;
	}
	var useCustom = document.getElementById('use_custom').value;
	var customConfig = document.getElementById('custom_config').value;
	if('true' == useCustom){
		if(customConfig == "" || customConfig == null){
			alert("Custom Configuration is blank.");
			return false;
		}
	}
	var createdURLvals= document.getElementById("created_urls").value;
	var y = 0;
	//if there are no createdURLs, then check to see if useCustom is false.  If it is, then fail.
	if(createdURLvals < 1 && useCustom == "false"){
		alert("There is no URL data.  Please insert URL data or use a custom configuration.");
		return false;

	}
	var URLExists = false;
	var URLsPopulated = 0;
	for(y = 0; y < createdURLvals; y++){
		var urlValue = document.getElementById("url_name_"+y+"");
		if(urlValue != null && useCustom == "false"){
			URLExists = true;
			if(!(urlValue.value == "") && urlValue.value){
				URLsPopulated++;
			}
		}
	}
	if (URLsPopulated == 0 && useCustom == "false") {
		alert("All of the URL names are blank.");
		return false;
	}
	if (!URLExists && useCustom == "false"){
		alert("There is no URL data.  Please insert URL data or use a custom configuration.");
		return false;
	}
	return true;
}