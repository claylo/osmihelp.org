from fabric.api import *

env.hosts = ['coj@funkatron.com:2202']
env.key_filename = ['~/.ssh/id_rsa']

def uname():
    run('uname')

def deploy():
    with cd('/var/www/osmihelp.org/'):
        run('git pull')
        run('npm install')
        run('bower install')

def update():
    with cd('/var/www/osmihelp.org/'):
        run('git pull')
