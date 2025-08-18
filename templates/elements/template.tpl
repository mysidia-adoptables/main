{include file="{$root}{$temp}{$theme}/header.tpl"}

    <body>
        <div id="wrapper">
            <div id="left">
                <img src="{$home}{$temp}{$theme}/inc/images/left.png">
                <ul>{$menu}</ul> 
            </div>
            <div id="center">
                <img src="{$home}{$temp}{$theme}/inc/images/center.png">
                <h1>{$document_title}</h1>
                <p>{$document_content}</p>
            </div>

            <div id="right">
                <img src="{$home}{$temp}{$theme}/inc/images/right.png">
                <p>{$sidebar}</p>
            </div>

            <div id="footer">
                {$footer}
                <p>Theme by <a href="http://sessions-st.net">Sessions Street</a></p> 
            </div>
        </div>
    </body>

</html>