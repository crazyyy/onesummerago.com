					
	function addCP( target )
	{
		jQuery(target).ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				jQuery(el).val(hex);
				jQuery(el).ColorPickerHide();
			},
			onBeforeShow: function () {
				jQuery(this).ColorPickerSetColor(this.value);
			}
		})
		.bind('keyup', function(){
			jQuery(this).ColorPickerSetColor(this.value);
		});

	};