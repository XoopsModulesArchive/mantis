BuildArchitectures: noarch
Summary: The Mantis Bug Tracker
Name: mantis
Version: 0.17.5
Release: 1
License: GPL
Group: Development/Tools
Vendor: Kenzaburo Ito <kenito@300baud.org>
Source: mantis-%{version}.tar.gz
URL: http://mantisbt.sourceforge.net/
Buildroot: %{_tmppath}/%{name}-root
Prefix: /var/www/mantis
Requires: php >= 4.0.3
Requires: php-mysql >= 4.0.3
Requires: mysql >= 3.23.2
Requires: webserver

%description
Mantis is a web-based bugtracking system.

%prep
%setup -q
%build
%install
rm -rf $RPM_BUILD_ROOT%{prefix}
install -d $RPM_BUILD_ROOT%{prefix}
mv *.php{,.sample} default images lang sql $RPM_BUILD_ROOT%{prefix}
%preun
rm -f %{prefix}/config_inc.php

%files
%defattr(-,root,root)
%doc doc/*
%config(noreplace) %{prefix}/default/config_inc1.php
%config(noreplace) %{prefix}/default/config_inc2.php
%prefix
