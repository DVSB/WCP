#!/usr/bin/python -O

import sys
from framework.wcfconfig import *
from framework.database import *
from framework.configuration import *
from framework.jobhandler import *

if len(sys.argv) == 2:
    path = sys.argv[1]
else:
    path = ".."

wcfconfig = wcfconfig(path)

db = database(wcfconfig)

config = configuration(db, wcfconfig)

jh = jobhandler(db, config)

jh.findPendingJobs()

jh.firePendingJobs()

jh.finishJobs()