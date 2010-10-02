import os.path, sys
from grp import getgrnam
from pwd import getpwnam
from re import *
from imp import load_module, find_module

def mkPath(typ, path, mode, uid, gid):
    """
    create given path (file, symlink, directory)
    @typ: string
    @path: string
    @mode: integer
    @uid: integer
    @gid: integer
    """
    
    if os.path.exists(path) == True:
        return True
    
    if typ == 'l':
        path = path.split('->')
        if os.path.exists(path[0]) == True:
            return True    
        os.symlink(path[1], path[0])
    elif typ == 'f':
        f = file(path, 'w+')
        f.close()
        os.chmod(path, mode)
        os.chown(path, uid, gid)
    #elif typ == 'cp':
    #    path = path.split('->')
    else:
        os.mkdir(path)
        os.chmod(path, mode)
        os.chown(path, uid, gid)
    
    return True

def getUID(uid):
    """
    will get uid from given string
    @guid: string
    """
    
    try:
        uid = int(uid)
    except:
        uid = getpwnam(uid).pw_uid

    return int(uid)

def getGID(gid):
    """
    will get gid from given string
    @guid: string
    """
    
    try:
        gid = int(gid)
    except:
        gid = getgrnam(gid).gr_gid

    return int(gid)

def parseOptions(string, config):
    options = findall("{(.+?)}", string, MULTILINE)
    
    for option in options:
        value = config.get(str.lower(option))
        if value is not None:
            string = string.replace("{" + option + "}", str(value))
            
    return string

def parseUser(string, user):
    options = findall("{(.+?)}", string, MULTILINE)
    
    user = dict((k.lower(), v) for k,v in user.iteritems())
    
    for option in options:
        if user.has_key(str.lower(option)):
            string = string.replace("{" + option + "}", str(user[str.lower(option)]))
            
    return string
    
def getActiveUsers(conf):
    users = conf.db.queryDict('SELECT * \
                               FROM   wcf' + conf.db.wcfnr + '_user user \
                               JOIN   cp' + conf.db.cpnr + '_user cpuser ON (user.userID = cpuser.userID)\
                               WHERE  banned = 0')      
    return users

def getUserOptions(conf, getoptions):
    options = ""
    for option in getoptions:
        options += str("'" + option + "'") + ','
        
    options = options.rstrip(',')

    options = conf.db.query("SELECT optionID \
                             FROM   wcf" + conf.db.wcfnr + "_user_option \
                             WHERE  optionName IN (" + options + ") AND packageID IN (" + conf.packages + ")") 
    return options

def loadModule(name, pathprefix):                
    # Fast path: see if the module has already been imported.
        
    try:
        return sys.modules[name]
    except KeyError:
        pass
        
    name = pathprefix + name

    # If any of the following calls raises an exception,
    # there's a problem we can't handle -- let the caller handle it.
    fp, pathname, description = find_module(name)
    
    try:
        return load_module(name, fp, pathname, description)
    finally:
        # Since we may exit via an exception, close fp explicitly.
        if fp:
            fp.close()