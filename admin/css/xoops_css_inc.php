<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@@300baud.org
# Modified for Modularization for Xoops 2 CMS
# Copyright (C) 2003  Michael van Dam - mvandam@caltech.edu

# This program is distributed under the terms and conditions of the GPL
# See the files README and LICENSE for details
?>
<style type="text/css">
    div#content {
        background-color: <?php echo $g_background_color ?>;
        color: <?php echo $g_background_font_color ?>;
        font-family: <?php echo $g_fonts ?>;
        font-size: <?php echo $g_font_normal ?>;
        margin-left: 4px;
        margin-right: 4px;
        margin-top: 6px;
        margin-bottom: 6px;
    }

    div#content
    p {
        font-family: <?php echo $g_fonts ?>;
        font-size: <?php echo $g_font_normal ?>
    }

    div#content
    p.center {
        text-align: center
    }

    div#content
    address {
        font-family: <?php echo $g_fonts ?>;
        font-size: <?php echo $g_font_small ?>
    }

    div#content
    span {
        font-family: <?php echo $g_fonts ?>;
        font-size: <?php echo $g_font_normal ?>;
    }

    div#content
    table {
    }

    div#content
    td {
        font-family: <?php echo $g_fonts ?>;
        font-size: <?php echo $g_font_normal ?>;
        padding: 4px;
        text-align: left
    }

    div#content
    a {
    }

    div#content
    a:active {
        color: <?php echo $g_active_font_color; ?>;
    }

    div#content
    a:link {
        color: <?php echo $g_unvisited_font_color; ?>;
    }

    div#content
    a:visited {
        color: <?php echo $g_visited_font_color; ?>;
    }

    div#content
    form {
        display: inline;
    }

    div#content
    input {
    }

    div#content
    textarea {
    }

    div#content
    select {
    }

    div#content
    span.print {
        font-size: <?php echo $g_font_small ?>;
    }

    div#content
    span.required {
        font-size: <?php echo $g_font_small ?>;
        color: <?php echo $g_required_font_color ?>;
        background-color: <?php echo $g_required_color ?>;
    }

    div#content
    span.small {
        font-size: <?php echo $g_font_small ?>;
        font-weight: normal;
    }

    div#content
    span.pagetitle {
        font-size: <?php echo $g_font_large ?>;
        font-weight: bold;
        text-align: center
    }

    div#content
    span.bold {
        font-weight: bold;
    }

    div#content
    span.italic {
        font-style: italic;
    }

    div#content
    span.italic {
        font-style: italic;
        font-size: 8pt;
    }

    div#content
    table.hide {
        width: 100%;
        border-color: <?php echo $g_background_color ?>;
        color: <?php echo $g_background_font_color ?>;
    }

    div#content
    table.width100 {
        width: 100%;
        border-color: <?php echo $g_table_border_color ?>;
        border-style: solid;
        border-width: 1px;
    }

    div#content
    table.width75 {
        width: 75%;
        border-color: <?php echo $g_table_border_color ?>;
        border-style: solid;
        border-width: 1px;
    }

    div#content
    table.width60 {
        width: 60%;
        border-color: <?php echo $g_table_border_color ?>;
        border-style: solid;
        border-width: 1px;
    }

    div#content
    table.width50 {
        width: 50%;
        border-color: <?php echo $g_table_border_color ?>;
        border-style: solid;
        border-width: 1px;
    }

    div#content
    td.center {
        text-align: center;
    }

    div#content
    td.left {
        text-align: left;
    }

    div#content
    td.right {
        text-align: right;
    }

    div#content
    td.category {
        background-color: <?php echo $g_category_title_color ?>;
        color: <?php echo $g_category_title_font_color ?>;
        font-weight: bold;
    }

    div#content
    td.col-1 {
        background-color: <?php echo $g_primary_color1 ?>;
        color: <?php echo $g_primary_font_color1 ?>;
    }

    div#content
    td.col-2 {
        background-color: <?php echo $g_primary_color2 ?>;
        color: <?php echo $g_primary_font_color2 ?>;
    }

    div#content
    td.form-title {
        background-color: <?php echo $g_form_title_color ?>;
        color: <?php echo $g_primary_font_color2 ?>;
    }

    div#content
    td.nopad {
        padding: 0px;
    }

    div#content
    td.spacer {
        background-color: <?php echo $g_spacer_color ?>;
        color: <?php echo $g_font_color ?>;
        font-size: 1pt;
        line-height: 0.1;
    }

    div#content
    td.small-caption {
        font-size: <?php echo $g_font_small ?>;
    }

    div#content
    td.print {
        font-size: <?php echo $g_font_small ?>;
        text-align: left;
        padding: 2px;
    }

    div#content
    td.print-category {
        font-size: <?php echo $g_font_small ?>;
        color: <?php echo $g_font_color ?>;
        font-weight: bold;
        text-align: right;
        padding: 2px;
    }

    div#content
    td.print-bottom {
        border-bottom: 1px solid #000000;
    }

    div#content
    td.print-spacer {
        background-color: <?php echo $g_spacer_color ?>;
        color: <?php echo $g_font_color ?>;
        font-size: 1pt;
        line-height: 0.1;
        padding: 0px;
    }

    div#content
    tr.center {
        text-align: center;
    }

    div#content
    tr.row-1 {
        background-color: <?php echo $g_primary_color1 ?>;
        color: <?php echo $g_primary_font_color1 ?>;
    }

    div#content
    tr.row-2 {
        background-color: <?php echo $g_primary_color2 ?>;
        color: <?php echo $g_primary_font_color2 ?>;
    }

    div#content
    tr.spacer {
        background-color: <?php echo $g_spacer_color ?>;
        color: <?php echo $g_font_color ?>;
    }

    div#content
    tr.row-category {
        background-color: <?php echo $g_category_title_color ?>;
        color: <?php echo $g_category_title_font_color ?>;
        font-weight: bold;
    }

    div#content
    tr.row-category2 {
        background-color: <?php echo $g_category_title_color ?>;
        color: <?php echo $g_category_title_font_color ?>;
    }

    div#content
    tr.print {
        vertical-align: top;
    }

    div#content
    tr.print-category {
        color: <?php echo $g_font_color ?>;
        font-weight: bold;
    }

    div#content
    td.login-info-left {
        width: 33%;
        padding: 0px;
        text-align: left;
    }

    div#content
    td.login-info-middle {
        width: 33%;
        padding: 0px;
        text-align: center;
    }

    div#content
    td.login-info-right {
        width: 33%;
        padding: 0px;
        text-align: right;
    }

    div#content
    span.login-username {
        font-style: italic;
    }

    div#content
    span.login-time {
        font-size: <?php echo $g_font_small ?>;
        font-style: italic;
    }

    div#content
    td.menu {
        background-color: <?php echo $g_menu_color ?>;
        color: <?php echo $g_menu_font_color ?>;
        text-align: center;
        width: 100%;
        padding: 1px;
    }

    div#content
    td.form-input {
        background-color: <?php echo $g_category_title_color ?>;
        color: <?php echo $g_category_title_font_color ?>;
    }

    div#content
    td.quick-summary-left {
        width: 50%;
        text-align: left;
    }

    div#content
    td.quick-summary-right {
        width: 50%;
        text-align: right;
    }

    div#content
    td.news-heading {
        background-color: <?php echo $g_primary_color1 ?>;
        color: <?php echo $g_primary_font_color1 ?>;
        text-align: left;
        border-bottom: 1px solid<?php echo $g_table_border_color ?>;
    }

    div#content
    td.news-body {
        padding: 16px;
    }

    div#content
    span.news-headline {
        font-weight: bold;
    }

    div#content
    span.news-date {
        font-style: italic;
        font-size: <?php echo $g_font_small ?>;
    }

    div#content
    a.news-email {
        font-size: <?php echo $g_font_small ?>;
    }

    div#content
    .small {
        font-size: 8pt;
    }

    div#content
    div {
        padding: 3px;
    }

    div#content
    div.menu {
        text-align: right;
        font-weight: normal;
        font-size: 8pt;
    }

    div#content
    div.category-title {
        font-weight: bold;
    }

    div#content
    div.left {
        text-align: left;
        display: inline;
        float: left;
    }

    div#content
    div.right {
        text-align: right;
        display: inline;
        float: right;
    }

    div#content
    div.menu {
        border: solid 1px #000000;
        width: 100%;
    }

    div#content
    div.menu-left {
        display: inline;
        float: left;
        background-color: #dddddd;
        width: 85%;
        height: 30px;
        vertical-align: bottom;
        text-align: center;
    }

    div#content
    div.menu-right {
        display: inline;
        float: right;
        white-space: nowrap;
        background-color: #0000ff;
        width: 15%;
        text-align: right;
        height: 30px;
        vertical-align: middle;
    }

    div#content
    div.login-info-left {
        display: inline;
        float: both;
        width: 33%;
        text-align: left;
    }

    div#content
    div.login-info-middle {
        display: inline;
        float: both;
        width: 34%;
        text-align: center;
    }

    div#content
    div.login-info-right {
        display: inline;
        float: both;
        width: 33%;
        text-align: right;
    }

    div#content
    div.news-body {
        padding: 16px;
        text-align: left;
        width: 50%;
        border: solid 1px #000000;
    }

</style>
<!-- For Netscape 4.x -->
<script language="JavaScript" type="text/javascript">
    <!--
    if (document.layers) {
        document.write('<style>td{padding:0px;}</style>')
    }
    //-->
</script>
<noscript></noscript>
