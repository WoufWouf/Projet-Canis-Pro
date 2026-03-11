# Projet-Canis-Pro
Site de gestion de cours, seances, chiens, membres car les dogos c'est les plus pipou-chou au monde (pas autant que les chats par contre)&lt;3


# Git
Si probleme quand push faire :
```
bash
git pull origin main --rebase
```

Ajout :
```
bash
git add .
```
Commit :
```
bash
git commit -m "Message décrivant les changements"
```
push :
```
bash
git push --set-upstream origin main
```

# PHP 8.4
Savoir si tu peut installer php8.4 :

```
bash
sudo apt update
apt policy php8.4
```

Ajouter le dépôt PHP:
```
bash
sudo apt update
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
```


 Clé :
```
bash

sudo mkdir -p /etc/apt/keyrings
curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo gpg --dearmor -o /etc/apt/keyrings/yarn.gpg
```

Puis recrée le repo proprement :

```
bash

echo "deb [signed-by=/etc/apt/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian stable main" | sudo tee /etc/apt/sources.list.d/yarn.list
```

Mettre a jour:
```
bash
sudo apt update
```

Installation php 8.4

```
bash
sudo apt install php8.4 php8.4-cli php8.4-common php8.4-mbstring php8.4-xml php8.4-curl php8.4-mysql php8.4-zip
```

Activer PHP 8.4 :

```
bash
sudo update-alternatives --set php /usr/bin/php8.4
```

```
bash
ls /usr/bin/php8*
```

```
bash
sudo update-alternatives --set php /usr/bin/php8.4
```

```
bash
which php
```

```
bash
type -a php
```

```
bash
sudo ln -sf /usr/bin/php8.4 /usr/local/bin/php
```

```
bash
export PATH="/usr/bin:$PATH"
```

```
bash
php -v
```

