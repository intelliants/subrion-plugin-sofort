<form action="https://www.sofort.com/payment/start" method="post" style="display: none">
	{foreach $formValues as $key => $value}
		<input type="hidden" name="{$key}" value="{$value}">
	{/foreach}
</form>