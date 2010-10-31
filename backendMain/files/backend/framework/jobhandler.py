import sys
from time import *
from functions import loadModule
from framework.PHPUnserialize import PHPUnserialize

class jobhandler(object):
    
    def __init__ (self, env):
        self.env = env
        self.jobs = {}
        
    def findPendingJobs(self):
        lastRun = localtime(self.env.config.get("last_run_backend"))
        currentTime = localtime()
        
        triggers = ['asap']
        
        if lastRun.tm_hour != currentTime.tm_hour:
            triggers.append('hourchange')
            
        if lastRun.tm_mday != currentTime.tm_mday:
            triggers.append('daychange')
            
        if lastRun.tm_wday == 6 and currentTime.tm_wday == 0:
            triggers.append('weekchange')
            
        if lastRun.tm_mon != currentTime.tm_mon:
            triggers.append('monthchange')
            
        if lastRun.tm_year != currentTime.tm_year:
            triggers.append('yearchange')
        
        self.env.logger.append('Search tasks for triggers: ' + " ".join(triggers))
        
        jobs = self.env.db.queryDict("SELECT * \
                                           FROM     cp" + self.env.cpnr + "_jobhandler_task jt \
                                           WHERE    nextExec IN ('" + "','".join(triggers) + "')\
                                           ORDER BY priority DESC, jobhandlerTaskID ASC")
        
        #lets try some optimization
        u = PHPUnserialize()
        for job in jobs:
            if job['data'] != '':
                job['data'] = u.unserialize(job['data'])
            
            if self.jobs.has_key(job['jobhandler']) == False:
                self.jobs[job['jobhandler']] = job
                self.jobs[job['jobhandler']]['jobhandlerTaskIDs'] = [str(job['jobhandlerTaskID'])]
                self.jobs[job['jobhandler']]['data'] = [job['data']]
            else:
                self.jobs[job['jobhandler']]['jobhandlerTaskIDs'].append(str(job['jobhandlerTaskID']))
                self.jobs[job['jobhandler']]['data'].append(job['data'])
        
        self.env.logger.writeJobs(self.jobs.keys())
        
    def firePendingJobs(self):
        for job in self.jobs:
            self.env.logger.append('Start task: ' + job)
            #try:
            module = loadModule(job, 'modules/')
            func = getattr(module, job)
            obj = func(self.jobs[job]['data'], self.env)
            self.jobs[job]['retVar'] = obj.run()
            #except Exception, e:
             #   self.jobs[job]['retVar'] = e
             #   self.env.logger.append("Error ocurred: " + str(self.jobs[job]))
            
            self.env.logger.append('Finished task ' + job + ' with status: ' + str(self.jobs[job]['retVar']))
        
    def finishJobs(self):
        for job in self.jobs:
            if self.jobs[job]['retVar'] != 'success':
                return "error ocurred"
            else:
                if self.jobs[job]['volatile'] == 1:
                    self.env.db.query("DELETE FROM cp" + self.env.cpnr + "_jobhandler_task \
                                   WHERE jobhandlerTaskID IN (" + ",".join(self.jobs[job]['jobhandlerTaskIDs']) + ")")
                else:
                    self.env.db.query("UPDATE cp" + self.env.cpnr + "_jobhandler_task \
                                   SET lastExec = UNIX_TIMESTAMP() \
                                   WHERE jobhandlerTaskID IN (" + ",".join(self.jobs[job]['jobhandlerTaskIDs']) + ")")
                      
        self.env.config.set("last_run_backend", int(time()))
        
        return "success"
        
        