{if $ShowBodyOnly eq 1}{* include ONLY $tplFile (body) file *}
{include file=$tplFile}
{else}{* include header/body/footer files *}
{include file="common/header.tpl"}
{* does the user have permission to view this page? *}
{if ($user->session->access_denied)}{include file='common/denied.tpl'}
{else}{include file=$tplFile}{/if}
{include file="common/footer.tpl"}
{/if}