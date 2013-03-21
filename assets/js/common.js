var DATE_FORMAT = 'dd-mm-yyyy';
var TIME_FORMAT = 'H:i';

var Site = {
    Host: web.host,
    IsValidEmail: function (Email) {
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        return emailPattern.test(Email);  
    },
    IsValidYear: function(Value) {
        var Result = true;
        
        Value = Value + '';
        Value = Value.replace(new RegExp('[^0-9]', 'gi'), '');
        
        if (Value.length != 4) {
            Result = false;
        }
        
        return Result;
    },
    IsValidPostalCode: function(Value) {
        var Result = true;
        
        Value = Value + '';
        Value = Value.replace(new RegExp('[^0-9]', 'gi'), '');
        
        if (Value.length != 5) {
            Result = false;
        }
        
        return Result;
    },
    GetTimeFromString: function(String) {
        String = $.trim(String);
        if (String == '') {
            return new Date();
        }
        
        var Data = new Date();
        var ArrayData = String.split('-');
        if (ArrayData[2] != null && ArrayData[2].length == 4) {
            Data = new Date(ArrayData[2] + '-' + ArrayData[1] + '-' + ArrayData[0]);
        }
        
        return Data;
    },
	SwapYearDay: function(String) {
		var Temp = Site.GetTimeFromString(String);
		var Result = Temp.getFullYear() + '-' + Temp.getMonth() + '-' + Temp.getDate();
		return Result;
	},
    Form: {
		InlineWarning: function(Input) {
			Input.parent('td').append('<div class="CntWarning">' + Input.attr('alt') + '</div>');
		},
        Start: function(Container) {
            var Input = jQuery('#' + Container + ' input');
            for (var i = 0; i < Input.length; i++) {
                if (Input.eq(i).hasClass('datepicker')) {
                    Input.eq(i).datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true, yearRange: 'c-20:c+10' });
                }
                else if (Input.eq(i).hasClass('integer') || Input.eq(i).hasClass('postalcode')) {
                    Input.eq(i).keyup(function(Param) {
						var Value = jQuery(this).val();
                        Value = Value.replace(new RegExp('[^0-9\.]', 'gi'), '');
						
						if (Param.keyCode == 16 || Param.keyCode == 17 || Param.keyCode == 18 || Param.ctrlKey || Param.shiftKey) {
							return true;
						}
						
						jQuery(this).val(Value);
                    });
                }
				else if (Input.eq(i).hasClass('alphabet')) {
					Input.eq(i).keyup(function(Param) {
						var Value = jQuery(this).val();
						Value = Value.replace(new RegExp('[^a-z\ ]', 'gi'), '');
						
						if (Param.keyCode == 16 || Param.keyCode == 17 || Param.keyCode == 18 || Param.ctrlKey || Param.shiftKey) {
							return true;
						}
						
						jQuery(this).val(Value);
					});
				}
				else if (Input.eq(i).hasClass('float')) {
					Input.eq(i).keyup(function(Param) {
						var Value = jQuery(this).val();
						Value = Value.replace(new RegExp('[^0-9\.]', 'gi'), '');
						
						if (Param.keyCode == 16 || Param.keyCode == 17 || Param.keyCode == 18 || Param.ctrlKey || Param.shiftKey) {
							return true;
						}
						
						jQuery(this).val(Value);
					});
				}
            }
        },
        Validation: function(Container, Param) {
			Param.Inline = (Param.Inline == null) ? false : Param.Inline;
			
            var ArrayError = [];
			jQuery('.CntWarning').remove();
            
            var Input = jQuery('#' + Container + ' input');
            for (var i = 0; i < Input.length; i++) {
                Input.eq(i).removeClass('ui-state-highlight');
                
                if (Input.eq(i).hasClass('required')) {
                    var Value = jQuery.trim(Input.eq(i).val());
                    
                    if (Value == '') {
                        Input.eq(i).addClass('ui-state-highlight');
                        ArrayError[ArrayError.length] = Input.eq(i).attr('alt');
						if (Param.Inline) Site.Form.InlineWarning(Input.eq(i));
                    }
                }
                if (Input.eq(i).hasClass('integer') || Input.eq(i).hasClass('datepicker')) {
                    var Value = jQuery.trim(Input.eq(i).val());
                    var ValueResult = Value.replace(new RegExp('[^0-9\-]', 'gi'), '');
                    
                    if (Value != ValueResult) {
                        Input.eq(i).addClass('ui-state-highlight');
                        ArrayError[ArrayError.length] = Input.eq(i).attr('alt');
						if (Param.Inline) Site.Form.InlineWarning(Input.eq(i));
                    }
                }
                if (Input.eq(i).hasClass('datepicker')) {
                    var Result = true;
                    var Value = jQuery.trim(Input.eq(i).val());
                    var ArrayValue = Value.split('-');
                    
                    if (Value.length == 0) {
                        Result = true;
                    } else if (ArrayValue.length != 3) {
                        Result = false;
                    } else if (ArrayValue[0] == '' || ArrayValue[1] == '' || ArrayValue[2] == '') {
                        Result = false;
                    }
                    
                    if (!Result) {
                        Input.eq(i).addClass('ui-state-highlight');
						if (Param.Inline) Site.Form.InlineWarning(Input.eq(i));
                        ArrayError[ArrayError.length] = Input.eq(i).attr('alt');
                    }
                }
                if (Input.eq(i).hasClass('email') && ! Site.IsValidEmail(Input.eq(i).val())) {
					if (Input.eq(i).val() != '') {
						Input.eq(i).addClass('ui-state-highlight');
						ArrayError[ArrayError.length] = Input.eq(i).attr('alt');
						if (Param.Inline) Site.Form.InlineWarning(Input.eq(i));
					}
                }
                if (Input.eq(i).hasClass('postalcode') && (Input.eq(i).val().length != 0 && Input.eq(i).val().length != 5)) {
                    Input.eq(i).addClass('ui-state-highlight');
                    ArrayError[ArrayError.length] = Input.eq(i).attr('alt');
					if (Param.Inline) Site.Form.InlineWarning(Input.eq(i));
                }
                if (Input.eq(i).hasClass('year') && (Input.eq(i).val().length != 0 && Input.eq(i).val().length != 4)) {
                    Input.eq(i).addClass('ui-state-highlight');
                    ArrayError[ArrayError.length] = Input.eq(i).attr('alt');
					if (Param.Inline) Site.Form.InlineWarning(Input.eq(i));
                }
            }
            
            var Select = jQuery('#' + Container +' select');
            for (var i = 0; i < Select.length; i++) {
                if (Select.eq(i).hasClass('required') && (Select.eq(i).val() == '' || Select.eq(i).val() == '-')) {
                    Select.eq(i).addClass('ui-state-highlight');
                    ArrayError[ArrayError.length] = Select.eq(i).attr('alt');
					if (Param.Inline) Site.Form.InlineWarning(Select.eq(i));
                } else {
                    Select.eq(i).removeClass('ui-state-highlight');
                }
            }
            
            var TextArea = jQuery('#' + Container +' textarea');
            for (var i = 0; i < TextArea.length; i++) {
                var Value = TextArea.eq(i).val();
                Value = jQuery.trim(Value);
                
                if (TextArea.eq(i).hasClass('required') && TextArea.eq(i).val() == '') {
                    TextArea.eq(i).addClass('ui-state-highlight');
                    ArrayError[ArrayError.length] = TextArea.eq(i).attr('alt');
                } else {
                    TextArea.eq(i).removeClass('ui-state-highlight');
                }
            }
            
            return ArrayError;
        },
        GetValue: function(Container) {
			var Prefix = '';
            var Data = Object();
			
			var PrefixCheck = Container.substr(0, 1);
			if (! Func.InArray(PrefixCheck, ['.', '#'])) {
				Container = '#' + Container;
			}
            
            var Input = jQuery(Container + ' input, ' + Container + ' select, ' + Container + ' textarea');
            for (var i = 0; i < Input.length; i++) {
				if (Input.eq(i).attr('type') == 'checkbox') {
					var Checked = Input.eq(i).attr('checked');
					if (typeof(Checked) == 'string' && Checked == 'checked') {
						Data[Input.eq(i).attr('name')] = Input.eq(i).val();
					} else {
						Data[Input.eq(i).attr('name')] = '0';
					}
				} else {
					Data[Input.eq(i).attr('name')] = Input.eq(i).val();
				}
            }
            
            return Data;
        }
    }
}

var Func = {
	ArrayToJson: function(Data) {
		var Temp = '';
		for (var i = 0; i < Data.length; i++) {
			Temp = (Temp.length == 0) ? Func.ObjectToJson(Data[i]) : Temp + ',' + Func.ObjectToJson(Data[i]);
		}
		return '[' + Temp + ']';
	},
	InArray: function(Value, Array) {
		var Result = false;
		for (var i = 0; i < Array.length; i++) {
			if (Value == Array[i]) {
				Result = true;
				break
			}
		}
		return Result;
	},
	IsEmpty: function(value) {
		var Result = false;
		if (value == null || value == 0) {
			Result = true;
		} else if (typeof(value) == 'string') {
			value = Func.Trim(value);
			if (value.length == 0) {
				Result = true;
			}
		}
		
		return Result;
	},
	ObjectToJson: function(obj) {
		var str = '';
		for (var p in obj) {
			if (obj.hasOwnProperty(p)) {
				if (obj[p] != null) {
					str += (str.length == 0) ? str : ',';
					str += '"' + p + '":"' + obj[p] + '"';
				}
			}
		}
		str = '{' + str + '}';
		return str;
	},
	SwapDate: function(Value) {
		if (Value == null) {
			return '';
		}
		
		var ArrayValue = Value.split(' ');
		var ArrayDate = ArrayValue[0].split('-');
		if (ArrayDate.length != 3) {
			return '';
		}
		
		var date = ArrayDate[2] + '-' + ArrayDate[1] + '-' + ArrayDate[0];
		var time = (ArrayValue[1] == null) ? '' : ArrayValue[1];
		var result = (time.length > 0) ? date + ' ' + time : date;
		
		return result;
	},
	Trim: function(value) {
		return value.replace(/^\s+|\s+$/g,'');
	},
	GetStringFromDate: function(Value) {
		if (Value == null) {
			return '';
		} else if (typeof(Value) == 'string') {
			return Value;
		}
		
		var Day = Value.getDate();
		var DayText = (Day.toString().length == 1) ? '0' + Day : Day;
		var Month = Value.getMonth() + 1;
		var MonthText = (Month.toString().length == 1) ? '0' + Month : Month;
		var Date = DayText + '-' + MonthText + '-' + Value.getFullYear();
		return Date;
	},
	GetStringFilter: function(obj) {
		var str = '';
		for (var p in obj) {
			if (obj.hasOwnProperty(p)) {
				if (obj[p] != null && obj[p].length > 0) {
					str += (str.length == 0) ? str : ',';
					str += '{"type":"string","value":"' + obj[p] + '","field":"' + p + '"}';
				}
			}
		}
		return '[' + str + ']';
	},
	Feature: function(p) {
		p.FilterObject = {}
		p.LastValue = p.FirstValue;
		
		$('#' + p.Container + ' .GridFilter').change(function() {
			p.FilterObject[p.LastValue] = $('#' + p.Container + ' input[name="namelike"]').val();
			
			var NextValue = $('#' + p.Container + ' .GridFilter').val();
			$('#' + p.Container + ' input[name="namelike"]').val((p.FilterObject[NextValue] != null) ? p.FilterObject[NextValue] : '');
			
			p.LastValue = NextValue;
		});
		$('#' + p.Container + ' .Reset').click(function() {
			p.FilterObject = {};
			$('#' + p.Container + ' input[name="namelike"]').val('');
			$('#' + p.Container + ' .GridFilter').change();
			var StringFilter = Func.GetStringFilter(p.FilterObject);
			
			$('#' + p.Container + ' input[name="PAGE_ACTIVE"]').val(1);
			$('#' + p.Container + ' input[name="PAGE_FILTER"]').val(StringFilter);
			p.LoadGrid({});
		});
		$('#' + p.Container + ' .Search').click(function() {
			$('#' + p.Container + ' .GridFilter').change();
			var StringFilter = Func.GetStringFilter(p.FilterObject);
			
			$('#' + p.Container + ' input[name="PAGE_ACTIVE"]').val(1);
			$('#' + p.Container + ' input[name="PAGE_FILTER"]').val(StringFilter);
			p.LoadGrid({});
		});
		
		p.LoadGrid({});
	},
	ConfirmDelete: function(p) {
		bootbox.confirm("Apa anda yakin ?", function(result) {
			if (! result) {
				return;
			}
			
			// Check Url
			p.Url = (p.Url != null) ? p.Url : '';
			
			$.ajax({ type: "POST", url: Web.HOST + '/index.php' + p.Url, data: p.Data }).done(function( RawResult ) {
				eval('var Result = ' + RawResult);
				
				$('#' + p.Container + ' .alert').remove();
				var Content = '<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Notification! </strong>' + Result.Message + '</div>';
				$('#' + p.Container).prepend(Content);
				if (Result.QueryStatus == 1) {
					p.LoadGrid({});
				}
			});
		});
	},
	InitForm: function(p) {
		// Date Picker
		$(p.Container + ' .datepicker').datepicker({ format: DATE_FORMAT });
		
		// Autogrow
		$(p.Container + ' .autogrow').autogrow();
		
		// Tooltips
		$(p.Container).find("label").tooltip({placement:'left'});
		$(p.Container + ' input, ' + p.Container + ' select, ' + p.Container + ' textarea').tooltip({ placement: 'right' });
		
		// add validation later
		return;
		
		// Validation
		$(p.Container + ' form').validate({
			onkeyup: false, errorClass: 'error', validClass: 'valid',
			highlight: function(element) { $(element).closest('div').addClass("f_error"); },
			unhighlight: function(element) { $(element).closest('div').removeClass("f_error"); },
			errorPlacement: function(error, element) { $(element).closest('div').append(error); },
			rules: p.rule
		});
	},
	InitUpload: function(p) {
		var uploader = new plupload.Uploader({
			runtimes : 'gears,html5,flash,silverlight,browserplus',
			browse_button : p.browse_button,
			container : p.container,
			max_file_size : '10mb',
			url : web.host + 'index.php/files/upload',
			flash_swf_url : web.host + 'assets/js/plupload/js/plupload.flash.swf',
			silverlight_xap_url : web.host + 'assets/js/plupload/js/plupload.silverlight.xap',
			filters : [
				{title : "Image files", extensions : "jpg,jpeg,gif,png"},
				{title : "Compressed files", extensions : "zip,gz,tar,bz2"},
				{title : "Document files", extensions : "pdf,doc,xls,docx,xlsx,pptx,ppt,odt,txt,rtf"}
			],
			resize : {width : 320, height : 240, quality : 90}
		});
		
		$('#' + p.container + ' .uploadfiles').click(function(e) {
			if ( $('#' + p.container + ' .filelist .addedfile').length > 0 )
				uploader.start();
			return false;
		});
		uploader.init();
		
		uploader.bind('FilesAdded', function(up, files) {
			$.each(files, function(i, file) {
				var ext = getExt(file.name);
				var raw =
					'<div class="addedfile uploadfile '+ext+'" id="' + file.id + '">' +
					'<span class="filename">' + file.name + '</span> (' + plupload.formatSize(file.size) + ') <b></b>' +
					'</div>';
				$('#' + p.container + ' .filelist').append(raw);
				$('#' + p.container + ' .uploadfiles').click();
			});
			up.refresh(); // Reposition Flash/Silverlight
		});
		
		uploader.bind('UploadProgress', function(up, file) {
			$('#' + file.id + " b").html(file.percent + "%");
		});
		
		uploader.bind('Error', function(up, err) {
			var raw = "<div class='alert alert-error'>Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : "") + "</div>";
			$('#' + p.container + ' .filelist').append(raw);
			up.refresh(); // Reposition Flash/Silverlight
		});
		
		uploader.bind('FileUploaded', function(up, file, jsonresp) {
			if (window.console)
				console.log(['filesuploaded', file, jsonresp]);
			
			var json = eval('('+jsonresp.response+')');
			var div = $("#"+file.id);
			div.removeClass('addedfile').addClass('completefile').find('b').html("100%").after('<input type="hidden" name="' + p.attachment + '" value="'+json.result+'">');
		});
	},
	SetValue: function(Param) {
		$.ajax({
			type: "POST", url: Web.HOST + '/index.php/combo', data: Param.data
		}).done(function( RawResult ) {
			eval('var Result = ' + RawResult);
			
			var Temp = '<option>-</option>';
			for (var i = 0; i < Result.length; i++) {
				Temp += '<option value="' + Result[i][Combo[Param.data.Action]['Value']] + '">' + Result[i][Combo[Param.data.Action]['Display']] + '</option>';
			}
			Param.combo.html(Temp);
			
			if (Param.value != null)
				Param.combo.val(Param.value);
		});
	}
}