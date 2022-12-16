# Портал

## Установка
```
тут будет инструкция по развёртыванию на машине разработчика
```

## Фронтэнд

Для сборки фронтенда нужен `node.js` и `npm`

```
npm i
gulp
```

## Для утановки `node.js` на Ubuntu:

```
curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash -
sudo apt-get install -y nodejs
sudo apt-get install -y build-essential
touch ~/.bashrc && echo 'export PATH=./node_modules/.bin:$PATH' >> ~/.bashrc && export PATH=./node_modules/.bin:$PATH
```

Для других систем [тут](https://nodejs.org/en/download/package-manager/)

## Особенности проекта

* Разработка ведется в папке blocks
* Весь код разделен на модули (блоки) в соответствии с методологией [БЭМ](https://ru.bem.info/methodology/quick-start/)
* итоговая статика (js, css, шрифты и картинки) находятся в папке `web/themes/public`;  генерятся автоматически, ПРАВИТЬ ИХ ВРУЧНУЮ НЕ НУЖНО.

## Работа со сборщиком

* Собрать для разработки `gulp dev`
* Собрать для продакшена `gulp`
* автоматически собирать статику при разработке `gulp watch` (не для продакшена)
* установить nvm по инструкции https://tecadmin.net/install-nvm-macos-with-homebrew/
* Если nwm не найден в терминале прописать - 
* export NVM_DIR="$HOME/.nvm"
* [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" in each ne
* текущая версия gulp работает с версией node < 12
* Для запуска необходим nvm, последняя сборка для продакшена делалась на node -v11.15.0 В консоле 
* Open the terminal window inside VS Code and run node -v. You'll get for instance v10.12.0.
* Open a terminal window outside VS Code Change your node version with nvm (ie. nvm use v12.14.0)
* Cmd+ Shift + p and choose Preferences > Open Settings (JSON)
* Add "terminal.integrated.shellArgs.osx": [] to your user config
* Cmd+ Shift + p and choose Shell Command: Install 'code' command in PATH
* Close VS Code.
* Open a terminal window and run code. This will open VS Code with a new and updated bash / zsh session.
* Open the terminal window inside VS Code and run node -v. You'll get v12.14.0.
