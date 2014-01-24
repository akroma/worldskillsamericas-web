jQuery(document).ready(function($) {	
	if ($(".xcrud").size()) {
		var xcrud;
		$.ajaxSetup({
			type: "post",
			url: xcrud_url + "/xcrud_ajax.php",
			error: function() {
				$(xcrud).find(".xcrud-overlay").stop(true, true).css("display", "none");
				alert(undefined_error);
			},
			beforeSend: function() {
				$(xcrud).find(".xcrud-overlay").width($(xcrud).parent(".xcrud-container").width()).stop(true, true).fadeTo(300, 0.6);
			},
			success: function(data) {
				xcrud = $(xcrud).parent(".xcrud-container");
				$(xcrud).html(data);
				$(window).trigger("ajaxload");
                $(document).trigger("ajaxload");
				$(xcrud).find(".xcrud-datepicker").trigger("ajaxload");
				$(xcrud).find("textarea.xcrud-texteditor").trigger("loadeditor");
				$(xcrud).find("div.xcrud-googlemap").trigger("loadgogglemap");
				xcrud = $(xcrud).children(".xcrud-ajax");
                //show_notice('Success',xcrud);
			},
			complete: function() {
				$(xcrud).find(".xcrud-overlay").stop(true, true).css("display", "none");
			},
			dataType: "html",
			cache: false
		});
		$(".xcrud").on("change", ".xcrud-limit-list", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			data.limit = $(this).val();
			$.ajax({
				data: data
			});
		});
		$(".xcrud").on("click", ".xcrud-pagination a", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			data.start = $(this).data("start");
			$.ajax({
				data: data
			});
			return false;
		});
		$(".xcrud").on("click", ".xcrud-column", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			data.order = $(this).data("column");
			data.direct = $(this).data("order-dir");
			$.ajax({
				data: data
			});
			return false;
		});
		$(".xcrud").on("click", ".xcrud-return,.xcrud-search-go", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			$.ajax({
				data: data
			});
			return false;
		});
		$(".xcrud").on("click", ".xcrud-toggle", function() {
			var data = list_data($(this).closest(".xcrud").find(".xcrud-ajax"));
			var closed = $(this).hasClass("closed");
			if (closed) {
				$(this).removeClass("closed");
				$(xcrud).parent(".xcrud-container").stop(true, true).slideDown(300,function(){
				    $(window).trigger("xslide");
				});
			} else {
				$(this).addClass("closed");
				$(xcrud).parent(".xcrud-container").stop(true, true).slideUp(300);
			}
			return false;
		});
		$(".xcrud").on("click", ".xcrud-edit", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			data.task = "edit";
			data.primary = $(this).data("primary");
			$.ajax({
				data: data
			});
			return false;
		});
        $(".xcrud").on("click", ".xcrud-detail-view", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			data.task = "view";
			data.primary = $(this).data("primary");
			$.ajax({
				data: data
			});
			return false;
		});
		$(".xcrud").on("click", ".xcrud-clone", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			data.task = "clone";
			data.primary = $(this).data("primary");
			$.ajax({
				data: data
			});
			return false;
		});
		$(".xcrud").on("click", ".xcrud-remove", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			data.task = "remove";
			data.primary = $(this).data("primary");
			var parent_act = $(this).data("parent-act");
			var el = $(this);
			if ($(this).hasClass("xcrud-confirm")) {
				if (confirm(deleting_confirm)) {
					if (parent_act) {
						$.ajax({
							data: data,
							success: function() {
								$(el).closest(".xcrud").parent().closest(".xcrud").find("a.xcrud-" + parent_act + ":first").click();
							}
						});
					} else {
						$.ajax({
							data: data
						});
					}
				} else
				return false;
			} else {
				$.ajax({
					data: data
				});
			}
			return false;
		});
		$(".xcrud").on("click", ".xcrud-add", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			data.task = "create";
			$.ajax({
				data: data
			});
			return false;
		});
		$(".xcrud").on("click", ".xcrud-save,.xcrud-save-return,.xcrud-save-new,.xcrud-save-edit", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			data.task = "save";
			data.after = $(this).data('after');
			data.primary = $(this).data('primary');
			data.postdata = {};
			var error = 0;
			var uni = {};
			try {
				tinyMCE.triggerSave()
			} catch (e) {}
			$(xcrud).find(".xcrud-input").each(function() {
				//$(this).removeClass('error');
				$(this).closest("tr").removeClass('error');
				$(this).parent(".xcrud-file-container").removeClass('error');
				data.postdata[$(this).attr('name')] = ($(this).data("type") == "bool") ? ($(this).prop("checked") ? 1 : 0) : $(this).val();
				var required = $(this).data('required');
				var pattern = $(this).data('pattern');
				if (required) {
					if (!validation_required($(this).val(), required)) {
						error = 1;
						//$(this).addClass('error');
						$(this).closest("tr").addClass('error');
						$(this).parent(".xcrud-file-container").addClass('error');
					}
				}
				if (pattern && error == 0 && $.trim($(this).val()).length > 0) {
					if (!validation_pattern($(this).val(), pattern)) {
						error = 1;
						//$(this).addClass('error');
						$(this).closest("tr").addClass('error');
					}
				}
				if ($(this).hasClass("unique")) {
					uni[$(this).attr('name')] = {
						"key": $(this).attr('name'),
						"val": $(this).val()
					};
				}
			});
			if (error == 1) {
				alert(validation_error);
				return false;
			}
			check_unique(uni, data);
			return false;
		});

		function check_unique(uni, data) {
			var i = 0;
			uni = $.map(uni, function(val) {
				if (i == 0) {
					data.field = val.key;
					data.value = val.val;
					data.task = "unique";
					$.ajax({
						dataType: "json",
						data: data,
						success: function(ret) {
							$(xcrud).find(".xcrud-data[name=key]").val(ret.key);
							data.key = ret.key;
							if (ret.unique == 0) {
								$(xcrud).find(".xcrud-input[name='" + val.key + "']").closest('tr').addClass("error");
								alert(unique_error);
								return false;
							} else {
								check_unique(uni, data);
							}
						}
					});
					i++;
					return null;
				} else {
					i++;
					return val;
				}
			});
			if (i == 0) {
				data.task = "save";
				$.ajax({
					data: data
				});
			}
		}
		$(".xcrud").on("keypress", ".xcrud-details input.xcrud-input[type='text'],.xcrud-details input.xcrud-input[type='password']", function(e) {
			var pattern = $(this).data('pattern');
			if (pattern) {
				var code = e.which;
				if (code < 32 || e.ctrlKey || e.altKey) return true;
				var val = String.fromCharCode(code);
				switch (pattern) {
				case 'alpha':
					reg = /^([a-z])+$/i;
					return reg.test(val);
					break;
				case 'alpha_numeric':
					reg = /^([a-z0-9])+$/i;
					return reg.test(val);
					break;
				case 'alpha_dash':
					reg = /^([-a-z0-9_-])+$/i;
					return reg.test(val);
					break;
				case 'numeric':
				case 'integer':
				case 'decimal':
					reg = /^[0-9\.\-+]+$/;
					return reg.test(val);
					break;
				case 'natural':
					reg = /^[0-9]+$/;
					return reg.test(val);
					break;
				}
			}
			return true;
		});
		$(".xcrud").on("ajaxload", ".xcrud-datepicker", function() {
			var format_id = $(this).data("type");            
			switch (format_id) {
			case 'datetime':
			case 'timestamp':
				$(this).datetimepicker({
					showSecond: true,
					timeFormat: "hh:mm:ss",
					dateFormat: "yy-mm-dd",
					changeMonth: true,
					changeYear: true
				});
				break;
			case 'time':
				$(this).timepicker({
					showSecond: true,
					timeFormat: "HH:mm:ss"
				});
				break;
			case 'date':
			default:
				$(this).datepicker({
					dateFormat: "yy-mm-dd",
					changeMonth: true,
					changeYear: true,
                    onClose: function( selectedDate ) {
                       var range_start = $(this).data("rangestart");
                       var range_end = $(this).data("rangeend");
                       if(range_start){
                            var target = $(this).closest(".xcrud-ajax").find('input[name="'+range_start+'"]');
                            $(target).datepicker( "option", "maxDate", selectedDate );
                       }
                       if(range_end){
                            var target = $(this).closest(".xcrud-ajax").find('input[name="'+range_end+'"]');
                            $(target).datepicker( "option", "minDate", selectedDate );
                       }
                    }
				});
			}
			$(".ui-datepicker").css("font-size", "11px");
		});
		$(".xcrud").on("loadeditor", "textarea.xcrud-texteditor", function() {
			if (tinymce_init && !$(this).hasClass("editor-loaded")) {
				$(".xcrud textarea.xcrud-texteditor").addClass("editor-loaded");
				if (tinymce_init_url) {
					window.setTimeout(function() {
						$.ajax({
							url: tinymce_init_url,
							type: "get",
							dataType: "script",
							success: function() {
								$(".xcrud-overlay").stop(true, true).css("display", "none");
							},
							cache: true
						});
					}, 300);
				} else {
					tinyMCE.init({
						mode: "textareas",
						theme: "advanced",
						editor_selector: "xcrud-texteditor",
						height: "400"
					});
				}
			}
		});
		$(".xcrud").on("loadgogglemap", "div.xcrud-googlemap", function() {
		  var cont = $(this);
			window.setTimeout(function() {
                var map, geocoder, infoWindow;
                var zoom = $(cont).data("zoom");
                var text = $(cont).data("text");
                var lng = $(cont).closest(".xcrud-ajax").find(".xcrud-input[name='"+$(cont).data("lng")+"']").val();
                var lat = $(cont).closest(".xcrud-ajax").find(".xcrud-input[name='"+$(cont).data("lat")+"']").val();
                var id = $(cont).attr('id');
                var options = {
        			zoom: zoom*1,
        			center: new google.maps.LatLng(lat*1, lng*1),
        			mapTypeId: google.maps.MapTypeId.ROADMAP
        		};
                map = new google.maps.Map(document.getElementById(id), options);
                getAddress(options.center);
        		// Attaching a click event to the map
        		google.maps.event.addListener(map, 'click', function(e) {
        			// Getting the address for the position being clicked
        			getAddress(e.latLng);
        		});
                
                function getAddress(latLng) {
                    $(cont).closest(".xcrud-ajax").find(".xcrud-input[name='"+$(cont).data("lat")+"']").val(latLng.lat());
                    $(cont).closest(".xcrud-ajax").find(".xcrud-input[name='"+$(cont).data("lng")+"']").val(latLng.lng());
            		// Check to see if a geocoder object already exists
            		if (!geocoder) {
            			geocoder = new google.maps.Geocoder();
            		}
            		// Creating a GeocoderRequest object
            		var geocoderRequest = {
            			latLng: latLng
            		}
            		geocoder.geocode(geocoderRequest, function(results, status) {
            			// If the infoWindow hasn't yet been created we create it
            			if (!infoWindow) {
            				infoWindow = new google.maps.InfoWindow();
            			}
            			// Setting the position for the InfoWindow
            			infoWindow.setPosition(latLng);
            			// Creating content for the InfoWindow
            			var content = '<h3>'+text+': ' + latLng.toUrlValue() + '</h3>';
            			// Check to see if the request went allright
            			if (status == google.maps.GeocoderStatus.OK) {
            				// Looping through the result
                        /*
                                for (var i = 0; i < results.length; i++) {
                                  if (results[0].formatted_address) {
                                    content += i + '. ' + results[i].formatted_address + '<br />';    			
                                  }
                                }
                                */
                            if (results[1])
                                    content += results[1].formatted_address + '<br />';
                            else if(results[0])
                                content += results[0].formatted_address + '<br />';
                        
            			} else {
            				content += '<p>No address found, try again.</p>';
            			}
            			// Adding the content to the InfoWindow
            			infoWindow.setContent(content);
            			// Opening the InfoWindow
            			infoWindow.open(map);
            		});
            	}
                
			}, 300);
		});
		$(window).on("resize load ajaxload xslide", function() {
			$(".xcrud").each(function() {
				if ($(this).find(".xcrud-list").width() > $(this).find(".xcrud-list-container").width()){
                    $(this).find(".xcrud-actions:not(xcrud-fix):first").width($(this).find(".xcrud-actions:not(xcrud-fix):first").width());
				    $(this).find(".xcrud-actions.xcrud-fix").addClass("xcrud-actions-fixed");
                }
				else
				$(this).find(".xcrud-actions").removeClass("xcrud-actions-fixed");
			});
		});
		$(".xcrud").on("click", ".xcrud-search-toggle", function() {
			$(this).hide(200);
			$(this).closest(".xcrud-ajax").find(".xcrud-search").show(200);
			return false;
		});
		$(".xcrud").on("click", ".xcrud-search-reset", function() {
			$(this).closest(".xcrud-ajax").find(".xcrud-phrase,.xcrud-daterange").val('');
			$(this).closest(".xcrud-ajax").find(".xcrud-search").css("display", "none");
			$(this).closest(".xcrud-ajax").find(".xcrud-search-toggle").show(200);
			var data = list_data($(this).closest(".xcrud-ajax"));
			$.ajax({
				data: data
			});
			return false;
		});
		$(".xcrud").on("keydown", ".xcrud-phrase", function(e) {
			if (e.which == 13) {
				var data = list_data($(this).closest(".xcrud-ajax"));
				$.ajax({
					data: data
				});
				return false;
			}
		});
		$(".xcrud").on("change", ".xcrud-upload", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			var container = $(this).closest("td").find(".xcrud-file-container").html('');
			var id = $(this).attr("id");
			var ext = getExtension($("#" + id).val());
			data.type = $(this).data("type");
			if (data.type == 'image') {
				switch (ext.toLowerCase()) {
				case 'jpg':
				case 'jpeg':
				case 'gif':
				case 'png':
					break;
				default:
					alert(image_type_error);
					$("#" + id).val('');
					return false;
					break;
				}
			}
			data.field = $(this).data("field");
			data.oldfile = $(container).find('.xcrud-input').val();
			data.task = "upload";
			$(xcrud).find(".xcrud-overlay").width($(xcrud).parent(".xcrud-container").width()).stop(true, true).fadeTo(300, 0.4);
			$.ajaxFileUpload({
				secureuri: false,
				fileElementId: id,
				data: data,
				success: function(data) {
					$(container).removeClass('error');
					$("#" + id).val('');
					$(xcrud).find(".xcrud-overlay").stop(true, true).css("display", "none");
					$(container).html(data);
					$(xcrud).find(".xcrud-data[name=key]").val($(container).find("input.new_key").val());
					$(container).find("input.new_key").remove();
				},
				error: function() {
					$(xcrud).find(".xcrud-overlay").stop(true, true).css("display", "none");
					alert(undefined_error);
				}
			});
			return false;
		});
		$(".xcrud").on("click", ".xcrud-remove-file", function() {
			var data = list_data($(this).closest(".xcrud-ajax"));
			var container = $(this).closest("td").find(".xcrud-file-container").val('');
			data.type = $(this).data("type");
			data.field = $(this).data("field");
			data.file = $(container).find('.xcrud-input').val();
			data.task = "remove_upload";
			$.ajax({
				data: data,
				success: function(data) {
					$(container).html(data);
					$(xcrud).find(".xcrud-data[name=key]").val($(container).find(".new_key").val());
					$(container).find(".new_key").remove();
				}
			});
			return false;
		});
		$(".xcrud").on("click", ".xcrud_modal", function() {
			var content = $(this).data("content");
			var header = $(this).data("header");
			$("body").append('<div id="xcrud-modal-window" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"> <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button><h3></h3></div><div class="modal-body"></div><div class="modal-footer"><button class="btn" data-dismiss="modal" aria-hidden="true">Close</button></div></div>');
			$("#xcrud-modal-window .modal-header h3").html(header);
			$("#xcrud-modal-window .modal-body").html(content);
			$('#xcrud-modal-window').on('hidden', function() {
				$('#xcrud-modal-window').remove();
			});
			$("#xcrud-modal-window").modal('show');
			return false;
		});
        

		function list_data(xcrud_inst) {
			var data = {};
			$(xcrud_inst).find(".xcrud-data").each(function() {
				data[$(this).attr("name")] = $(this).val();
			});
			xcrud = xcrud_inst;
			return data;
		}

		function validation_required(val, length) {
			return $.trim(val).length >= length;
		}

		function validation_pattern(val, pattern) {
			switch (pattern) {
			case 'email':
				reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
				return reg.test($.trim(val));
				break;
			case 'alpha':
				reg = /^([a-z])+$/i;
				return reg.test($.trim(val));
				break;
			case 'alpha_numeric':
				reg = /^([a-z0-9])+$/i;
				return reg.test($.trim(val));
				break;
			case 'alpha_dash':
				reg = /^([-a-z0-9_-])+$/i;
				return reg.test($.trim(val));
				break;
			case 'numeric':
				reg = /^[\-+]?[0-9]*\.?[0-9]+$/;
				return reg.test($.trim(val));
				break;
			case 'integer':
				reg = /^[\-+]?[0-9]+$/;
				return reg.test($.trim(val));
				break;
			case 'decimal':
				reg = /^[\-+]?[0-9]+\.[0-9]+$/;
				return reg.test($.trim(val));
				break;
			case 'natural':
				reg = /^[0-9]+$/;
				return reg.test($.trim(val));
				break;
			}
			return true;
		}

        $(window).on("ajaxload",function(){
            $('.xcrud .xcrud-view .xcrud-input[data-depend]').each(function(){
                var data = list_data($(this).closest(".xcrud-ajax"));
                var depend_on = $(this).data("depend");
                data.name = $(this).attr('name');
                data.value = $(this).val();
                data.task = 'depend';
                $('.xcrud-view').on('change','.xcrud-input[name="'+depend_on+'"]',function(){
                    data.dependval = $(this).val();
                    depend_query(data,$(this).closest(".xcrud"));
                });
                window.setTimeout(function(){$('.xcrud-view').find('.xcrud-input[name="'+depend_on+'"]').trigger('change');},50);
            });
        });
        
        $(window).on("ajaxload",function(){
            if($('.xcrud .xcrud-view .xcrud-tabs-ui').size()){
                window.setTimeout(function(){$('.xcrud .xcrud-view .xcrud-tabs-ui').tabs()},50);
            }
        });

		function getExtension(filename) {
			var parts = filename.split('.');
			return parts[parts.length - 1];
		}
        function depend_query(data,xcrud){
            $.ajax({
                data: data,
                success: function(input){
                    $(xcrud).find('.xcrud-input[name="'+data.name+'"]').replaceWith(input);
                    $(xcrud).find('.xcrud-input[name="'+data.name+'"]').triggerHandler('change');
                }
            });
        }
        function show_notice(text,xcrud){
            xcrud = $(xcrud).closest('.xcrud-container');
            var notice = '<div class="xcrud-notice">'+text+'</div>';
            $(xcrud).append(notice);
            $(xcrud).find(".xcrud-notice").stop(true,true).animate({height:40},300).delay(3000).slideUp(300,function(){
                $(this).remove();
            });
        }
        $(document).on("ajaxload ready",function(){
            if($('.xcrud .xcrud-tooltip-bs').size){
                $('.xcrud .xcrud-tooltip-bs').tooltip();
            }
            if($('.xcrud .xcrud-tooltip-ui').size){
                $('.xcrud .xcrud-tooltip-ui').tooltip();
            }
        });
        
        $(".xcrud").on("change",".xcrud-data.xcrud-filter",function(){
            var fields = null;
            var is_daterange = $(this).closest('.xcrud-search').find(".xcrud-daterange").size();
            var type = $(this).find("option:selected").data("type");
            if(type == 'date' && !is_daterange){
                fields = '<select class="xcrud-data xcrud-rangepreset xcrud-sp" name="column">';
                fields += '<option value="">- select -</option>';
                fields += '<option value="today">Today</option>';
                fields += '<option value="week1">This Week (Mon - Today)</option>';
                fields += '<option value="week2">This Week (Mon - Sun)</option>';
                fields += '<option value="week3">Last Week</option>';
                fields += '<option value="month1">This Month</option>';
                fields += '<option value="month2">Last Month</option>';
                fields += '<option value="month3">Last 3 Months</option>';
                fields += '<option value="month4">Last 6 Months</option>';
                fields += '<option value="year1">This Year</option>';
                fields += '<option value="year2">Last Year</option>';
                fields += '</select>';
                fields += '<input type="text" class="xcrud-data xcrud-daterange xcrud-sp" name="phrase[0]" value="" />';
                fields += '<input type="text" class="xcrud-data xcrud-daterange xcrud-sp" name="phrase[1]" value="" />';
            }else if(type != 'date' && is_daterange){
                fields = '<input type="text" class="xcrud-data xcrud-phrase xcrud-sp" name="phrase" value="" />';
            }
            if(fields){
                $(this).closest('.xcrud-search').find(".xcrud-sp").remove();
                $(this).closest('.xcrud-search').prepend(fields);
                $(document).trigger("datefieldsload");
            }
        });
        $(document).on("ajaxload ready datefieldsload",function(){
            if($(".xcrud-daterange").size()){
                $('.xcrud-daterange[name="phrase[0]"]').datepicker({
					dateFormat: "yy-mm-dd",
					changeMonth: true,
					changeYear: true,
                    maxDate:  $(this).closest('.xcrud-search').find('.xcrud-daterange[name="phrase[1]"]').val(),
                    onClose: function( selectedDate ) {
                       $(this).closest('.xcrud-search').find('.xcrud-daterange[name="phrase[1]"]').datepicker("option", "minDate", selectedDate);
                    },
                    onSelect: function( selectedDate ) {
                       $(this).closest('.xcrud-search').find('.xcrud-daterange[name="phrase[1]"]').datepicker("option", "minDate", selectedDate);
                    }
				});
                $('.xcrud-daterange[name="phrase[1]"]').datepicker({
					dateFormat: "yy-mm-dd",
					changeMonth: true,
					changeYear: true,
                    onClose: function( selectedDate ) {
                       $(this).closest('.xcrud-search').find('.xcrud-daterange[name="phrase[0]"]').datepicker("option", "maxDate", selectedDate);
                    },
                    onSelect: function( selectedDate ) {
                       $(this).closest('.xcrud-search').find('.xcrud-daterange[name="phrase[0]"]').datepicker("option", "maxDate", selectedDate);
                    }
				});
                $(".ui-datepicker").css("font-size", "11px");
                $(".xcrud").on("change",".xcrud-rangepreset",function(){
                    var preset = $(this).val();
                    var field0 = $(this).closest('.xcrud-search').find('.xcrud-daterange[name="phrase[0]"]');
                    var field1 = $(this).closest('.xcrud-search').find('.xcrud-daterange[name="phrase[1]"]');
                    switch(preset){
                        case 'today':
                            var start = new Date();
                            var end = new Date();
                            break;
                        case 'week1':
                            var d = new Date();
                            var day = d.getDay();
                            var start = new Date();
                            start.setDate(d.getDate()-day+1);
                            var end = new Date();
                            break;
                        case 'week2':
                            var d = new Date();
                            var day = d.getDay();
                            var start = new Date();
                            start.setDate(d.getDate()-day+1);
                            var end = new Date();
                            end.setDate(d.getDate()+7-day);
                            break;
                        case 'week3':
                            var d = new Date();
                            var day = d.getDay();
                            var start = new Date();
                            start.setDate(d.getDate()-day-6);
                            var end = new Date();
                            end.setDate(d.getDate()-day);
                            break;
                        case 'month1':
                            var start = new Date();
                            start.setDate(1);
                            var end = new Date();
                            end.setMonth(end.getMonth()+1);
                            end.setDate(0);
                            break;
                        case 'month2':
                            var start = new Date();
                            start.setMonth(start.getMonth()-1);
                            start.setDate(1);
                            var end = new Date();
                            end.setDate(0);
                            break;
                        case 'month3':
                            var start = new Date();
                            start.setMonth(start.getMonth()-3);
                            start.setDate(1);
                            var end = new Date();
                            end.setDate(0);
                            break;
                        case 'month4':
                            var start = new Date();
                            start.setMonth(start.getMonth()-6);
                            start.setDate(1);
                            var end = new Date();
                            end.setDate(0);
                            break;
                         case 'year1':
                            var start = new Date();
                            start.setFullYear(start.getFullYear());
                            start.setMonth(0);
                            start.setDate(1);
                            var end = new Date();
                            end.setMonth(11);
                            end.setDate(31);
                            break;
                         case 'year2':
                            var start = new Date();
                            start.setFullYear(start.getFullYear()-1);
                            start.setMonth(0);
                            start.setDate(1);
                            var end = new Date();
                            end.setFullYear(end.getFullYear()-1);
                            end.setMonth(11);
                            end.setDate(31);
                            break;
                        default:
                            return;
                            break;
                    }
                    $(field0).datepicker("setDate", start);
                    $(field1).datepicker("setDate", end);
                    start = null;
                    end = null;
                });
            }
        });

	}
});
jQuery.extend({
	print_window: function(print_win, xcrud) {
		var data = {};
		jQuery(xcrud).find(".xcrud-data").each(function() {
			data[jQuery(this).attr("name")] = jQuery(this).val();
		});
		data.task = 'print';
		jQuery.ajax({
			data: data,
			success: function(out) {
				print_win.document.open();
				print_win.document.write(out);
				print_win.document.close();
				jQuery(xcrud).find(".xcrud-data[name=key]").val(jQuery(print_win.document).find(".xcrud-data[name=key]").val());
				var ua = navigator.userAgent.toLowerCase();
				if ((ua.indexOf("opera") != -1)) { // opera fix
					jQuery(print_win).load(function() {
						print_win.print();
					});
				} else {
					jQuery(print_win).ready(function() {
						print_win.print();
					});
				}
			}
		});
	}
});
// file upl
jQuery.extend({
	createUploadIframe: function(id, uri) {
		var frameId = 'jUploadFrame' + id;
		var iframeHtml = '<iframe id="' + frameId + '" name="' + frameId + '" style="position:absolute; top:-9999px; left:-9999px"';
		if (window.ActiveXObject) {
			if (typeof uri == 'boolean') {
				iframeHtml += ' src="' + 'javascript:false' + '"';
			} else if (typeof uri == 'string') {
				iframeHtml += ' src="' + uri + '"';
			}
		}
		iframeHtml += ' />';
		jQuery(iframeHtml).appendTo(document.body);
		return jQuery('#' + frameId).get(0);
	},
	createUploadForm: function(id, fileElementId, data) {
		var formId = 'jUploadForm' + id;
		var fileId = 'jUploadFile' + id;
		var form = jQuery('<form  action="" method="POST" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data"></form>');
		if (data) {
			for (var i in data) {
				jQuery('<input type="hidden" name="' + i + '" value="' + data[i] + '" />').appendTo(form);
			}
		}
		var oldElement = jQuery('#' + fileElementId);
		var newElement = jQuery(oldElement).clone();
		jQuery(oldElement).attr('id', fileId);
		jQuery(oldElement).before(newElement);
		jQuery(oldElement).appendTo(form);
		jQuery(form).css('position', 'absolute');
		jQuery(form).css('top', '-1200px');
		jQuery(form).css('left', '-1200px');
		jQuery(form).appendTo('body');
		return form;
	},
	ajaxFileUpload: function(s) {
		s = jQuery.extend({}, jQuery.ajaxSettings, s);
		var id = new Date().getTime();
		var form = jQuery.createUploadForm(id, s.fileElementId, (typeof(s.data) == 'undefined' ? false : s.data));
		var io = jQuery.createUploadIframe(id, s.secureuri);
		var frameId = 'jUploadFrame' + id;
		var formId = 'jUploadForm' + id;
		if (s.global && !jQuery.active++) {
			jQuery.event.trigger("ajaxStart");
		}
		var requestDone = false;
		var xml = {};
		if (s.global) jQuery.event.trigger("ajaxSend", [xml, s]);
		var uploadCallback = function(isTimeout) {
			var io = document.getElementById(frameId);
			try {
				if (io.contentWindow) {
					xml.responseText = io.contentWindow.document.body ? io.contentWindow.document.body.innerHTML : null;
					xml.responseXML = io.contentWindow.document.XMLDocument ? io.contentWindow.document.XMLDocument : io.contentWindow.document;
				} else if (io.contentDocument) {
					xml.responseText = io.contentDocument.document.body ? io.contentDocument.document.body.innerHTML : null;
					xml.responseXML = io.contentDocument.document.XMLDocument ? io.contentDocument.document.XMLDocument : io.contentDocument.document;
				}
			} catch (e) {}
			if (xml || isTimeout == "timeout") {
				requestDone = true;
				var status;
				try {
					status = isTimeout != "timeout" ? "success" : "error";
					if (status != "error") {
						var data = jQuery.uploadHttpData(xml, s.dataType);
						if (s.success) s.success(data, status);
						if (s.global) jQuery.event.trigger("ajaxSuccess", [xml, s]);
					} else {}
				} catch (e) {
					status = "error";
				}
				if (s.global) jQuery.event.trigger("ajaxComplete", [xml, s]);
				if (s.global && !--jQuery.active) jQuery.event.trigger("ajaxStop");
				if (s.complete) s.complete(xml, status);
				jQuery(io).unbind();
				setTimeout(function() {
					try {
						jQuery(io).remove();
						jQuery(form).remove();
					} catch (e) {}
				}, 100);
				xml = null
			}
		};
		if (s.timeout > 0) {
			setTimeout(function() {
				if (!requestDone) uploadCallback("timeout");
			}, s.timeout);
		}
		try {
			var form = jQuery('#' + formId);
			jQuery(form).attr('action', s.url);
			jQuery(form).attr('method', 'POST');
			jQuery(form).attr('target', frameId);
			if (form.encoding) {
				jQuery(form).attr('encoding', 'multipart/form-data');
			} else {
				jQuery(form).attr('enctype', 'multipart/form-data');
			}
			jQuery(form).submit();
		} catch (e) {}
		var ttt = 0;
		var ua = navigator.userAgent.toLowerCase();
		if ((ua.indexOf("opera") != -1)) { // opera fix
			jQuery('#' + frameId).load(function() {
				ttt++;
				if (ttt == 2) {
					uploadCallback();
				}
			});
		} else {
			jQuery('#' + frameId).on("load", uploadCallback);
		}
		return {
			abort: function() {}
		};
	},
	uploadHttpData: function(r, type) {
		var data = !type;
		data = (type == "xml" || data) ? r.responseXML : r.responseText;
		if (type == "script") jQuery.globalEval(data);
		if (type == "json") eval("data = " + data);
		return data;
	}
});