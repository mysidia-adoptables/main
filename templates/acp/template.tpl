{include file="{$root}{$temp}{$theme}/header.tpl"}

    <body>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="2" class="banner">
                    <center>
                                   <a href="{$home}"><img id="banner" src="{$home}{$temp}{$theme}/media/acp-banner.png" alt="Mysidia PHP Adoptables" title="Mysidia Adoptables" /></a>
                    </center>
                </td>
            </tr>
            <tr>
                <th width="25%" id="logo"><strong>MyMysidia</strong> Admin</th>
                <th id="admin">Welcome Admin!</th>
            </tr>
            <tr>
                <td width="25%" id="menu">{$sidebar}</td>
                <td id="content">
                    <p><font size="5"><b>{$document_title}</b></font></p>
                    <hr>
                    <p>{$document_content}</p>
                </td>
            </tr>
        </table>

        <center>
            <b>MyMysidia</b> Powered By <a href="https://mysidiaadoptables.com">Mysidia Adoptables</a> &copy;Copyright 2011-2021.
        </center>
    </body>    
</html>