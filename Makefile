include Makefile.env

ps:
	docker-compose ps
up:
	docker-compose up -d
build:
	docker-compose build --no-cache --force-rm
db-set:
	docker-compose exec db bash -c 'mkdir /var/lib/mysql/sql && \
		touch /var/lib/mysql/sql/query.sql && \
		chown -R mysql:mysql /var/lib/mysql'

file-set:
	mkdir -p scripts/sql scripts/script data && \
		touch scripts/sql/query.sql scripts/script/set-query.sh && \
		cp env/.env.example .env && \
		mkdir .vscode && cp env/launch.json .vscode
launch:
	@make file-set
	@make publish-redisinsight
	@make build
	@make up
	@make db-set
useradd:
# web-root
	docker-compose exec web bash -c ' \
		useradd -s /bin/bash -m -u $$USER_ID -g $$GROUP_ID $$USER_NAME'
# db-root
	docker-compose exec db bash -c ' \
		useradd -s /bin/bash -m -u $$USER_ID -g $$GROUP_ID $$USER_NAME'
groupadd:
# web-root
	docker-compose exec web bash -c ' \
		groupadd -g $$GROUP_ID $$GROUP_NAME'
# db-root
	docker-compose exec db bash -c ' \
		groupadd -g $$GROUP_ID $$GROUP_NAME'
init:
	docker-compose up -d --build
	docker-compose exec web composer install
	docker-compose exec web cp .env.example .env
remake:
	@make destroy
	@make init
start:
	docker-compose start
stop:
	docker-compose stop
down:
	docker-compose down --remove-orphans
restart:
	@make stop
	@make start
reset:
	@make down
	@make up
rebuild:
	@make build
	docker-compose up -d
destroy:
	@make chown
	docker-compose down --rmi all --volumes --remove-orphans
	rm -rf data backend scripts && rm .env
	rm -rf infra/docker/redisinsight .vscode
destroy-volumes:
	docker-compose down --volumes --remove-orphans
# log
logs:
	docker-compose logs
logs-watch:
	docker-compose logs --follow
log-web:
	docker-compose logs web
log-web-watch:
	docker-compose logs --follow web
log-app:
	docker-compose logs app
log-app-watch:
	docker-compose logs --follow app
log-db:
	docker-compose logs db
log-db-watch:
	docker-compose logs --follow db
# web
web:
	docker-compose exec web bash
web-usr:
	docker-compose exec -u $(USER) web bash
# npm
npm:
	@make npm-install
npm-install:
	docker-compose exec web npm install
npm-dev:
	docker-compose exec web npm run dev
npm-watch:
	docker-compose exec web npm run watch
npm-watch-poll:
	docker-compose exec web npm run watch-poll
npm-hot:
	docker-compose exec web npm run hot
# yarn
yarn:
	docker-compose exec web yarn
yarn-install:
	@make yarn
yarn-dev:
	docker-compose exec web yarn dev
yarn-watch:
	docker-compose exec web yarn watch
yarn-watch-poll:
	docker-compose exec web yarn watch-poll
yarn-hot:
	docker-compose exec web yarn hot
# db
db:
	docker-compose exec db bash
db-usr:
	docker-compose exec -u $(USER) db bash
# sql
sql:
	docker-compose exec db bash -c 'mysql -u $$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE'
sql-root:
	docker-compose exec db bash -c 'mysql -u root -p'
sqlc:
	@make query
	docker-compose exec db bash -c 'mysql -u $$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE < /var/lib/mysql/sql/query.sql'
query:
	@make chown-data
	cp ./scripts/sql/query.sql ./data/sql/query.sql
# cp ./scripts/sql/query.sql ./_data/sql/query.sql
	@make chown-mysql
cp-sql:
	@make chown-data
	cp -r -n ./scripts/sql/** ./data/sql
# cp -r -n ./scripts/sql ./_data/sql
	@make chown-mysql
# redis
redis:
	docker-compose exec redis redis-cli --raw
publish-redisinsight:
	mkdir -p ./infra/docker/redisinsight/sessions
	sudo chown 1001 ./infra/docker/redisinsight/sessions
chown:
	@make chown-data
	@make chown-backend
# chown-web
chown-backend:
	sudo chown -R $(USER):$(GNAME) backend
chown-work:
	docker-compose exec web bash -c 'chown -R $$USER_NAME:$$GROUP_NAME /work'
# chown-db
chown-data:
	sudo chown -R $(USER):$(GNAME) data
chown-mysql:
	docker-compose exec db bash -c 'chown -R mysql:mysql /var/lib/mysql'
# git
git:
	git add .
	git commit -m $(msg)
	git push origin
g:
	@make git
git-msg:
	env | grep "msg"
# link
link:
	source
	ln -s `docker volume inspect $(rep)_db-store | grep "Mountpoint" | awk '{print $$2}' | awk '{print substr($$0, 2, length($$0)-3)}'` .
unlink:
	unlink _data
rep:
	env | grep "rep"
chown-volume:
	sudo chown -R $(USER):$(GNAME) ~/.local/share/docker/volumes
rm-data:
	@make chown-data
	rm -rf data
change-data:
	@make rm-data
	@make link
# docker
volume-ls:
	docker volume ls
volume-inspect:
	docker volume inspect $(rep)_db-store
# phpdotenvを使用する際必要
cpenv:
	docker cp ./env/dotenv.env `docker-compose ps -q web`:/work/.env
