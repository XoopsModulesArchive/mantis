<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id: compress_api.php,v 1.12 2003/03/15 22:09:55 jfitzell Exp $
# --------------------------------------------------------

###########################################################################
# Compression API
#
# Starts the buffering/compression (only if the compression option is ON)
# This method should be called after all possible re-directs and
#  access level checks.
###########################################################################

$g_compression_started = false;

# ----------------
# Check if compression should be enabled.
function compress_is_enabled()
{
    global $g_compression_started;

    #@@@ temporarily disable compression when using IIS because of

    #   issue #2953

    return ($g_compression_started
            && ON == config_get('compress_html') && OFF == config_get('use_iis') && 'ob_gzhandler' != ini_get('outputHandler') && extension_loaded('zlib')
            && !ini_get('zlib.output_compression'));
}

# ----------------
# Output Buffering handler that either compresses the buffer or just
#  returns it, depending on the return value of compress_is_enabled()
function compressHandler($p_buffer, $p_mode)
{
    if (compress_is_enabled()) {
        return ob_gzhandler($p_buffer, $p_mode);
    }

    return $p_buffer;
}

# ----------------
# Enable output buffering with compression.
function compress_enable()
{
    global $g_compression_started;

    $g_compression_started = true;
}

# ----------------
# Disable output buffering with compression.
function compress_disable()
{
    global $g_compression_started;

    $g_compression_started = false;
}
