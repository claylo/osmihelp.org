from fabric.api import *

env.hosts = ['coj@funkatron.com:2202']
env.key_filename = ['~/.ssh/id_rsa']

def uname():
    run('uname')

def deploy():
    with cd('/var/www'):
        run('sudo rm -rf osmihelp.org', warn_only=True)
        run('sudo mkdir osmihelp.org')
        run('sudo chown coj:coj osmihelp.org')
        run('rm -rf osmihelp.org/*')
        run('git clone https://github.com/OSMIHelp/osmihelp.org.git osmihelp.org')
    with cd('/var/www/osmihelp.org/'):
        run('chmod 777 cache')
        run('git pull')
        run('composer update --optimize-autoloader --no-dev')
        run('npm install')
        run('bower install')

def update():
    with cd('/var/www/osmihelp.org/'):
        run('git pull')
        run('rm cache/*')
        run('composer update --optimize-autoloader --no-dev')
