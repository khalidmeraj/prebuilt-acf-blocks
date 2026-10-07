var $j = jQuery.noConflict();

$j(document).ready(function () {
	var $accordionHeads = $j('.faq_accordion .accordion_title_head');

	if ($accordionHeads.length) {
		$accordionHeads.each(function () {
			$j(this).click(function () {
				var $this_inner = $j(this);
				var $thisPanel = $this_inner.closest('.faq_accordion_item').find('.accordion_content');
				var isOpen = $this_inner.hasClass('opened');

				// Close all other items
				$accordionHeads.not($this_inner).removeClass('opened').attr('aria-expanded', 'false');
				$j('.faq_accordion .accordion_content').not($thisPanel).slideUp(function () {
					$j(this).attr('hidden', true);
				});

				// Toggle the clicked item
				$this_inner.toggleClass('opened').attr('aria-expanded', isOpen ? 'false' : 'true');

				if (isOpen) {
					$thisPanel.slideUp(function () {
						$thisPanel.attr('hidden', true);
					});
				} else {
					$thisPanel.removeAttr('hidden').hide().slideDown();
				}
			});
		});
	}
});