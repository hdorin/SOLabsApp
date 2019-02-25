#!/bin/sh

sudo chown -R $1 mvc/

sudo chgrp www-data mvc/app/
sudo chmod 0750 mvc/app/

sudo chgrp www-data mvc/app/questions/
sudo chmod 0770 mvc/app/questions/

sudo chgrp www-data mvc/app/scp_cache/
sudo chmod 0770 mvc/app/scp_cache/

