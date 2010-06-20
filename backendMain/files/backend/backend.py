#!/usr/bin/python -O

import sys
from framework.jobhandler import jobhandler
from framework.env import env

if len(sys.argv) == 2:
    path = sys.argv[1]
else:
    path = "../.."

env = env(path)

jh = jobhandler(env)

jh.findPendingJobs()

jh.firePendingJobs()

success = jh.finishJobs()

env.close(success)