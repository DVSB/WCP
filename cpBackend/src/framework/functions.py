import os
import os.path
import sys
from grp import getgrnam
from pwd import getpwnam

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
    