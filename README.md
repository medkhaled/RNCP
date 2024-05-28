Configuration de projet:
	- Mise en place de l'environnemet de développement(PHP>8,Symfony>6,SGBD)
	- Installer les fixtures (fausses données)
		- composer require --dev orm-fixtures
		- composer require fakerphp/faker
	- Installer  Rate Limiter component pour se protoger contre "brute force login attacks"
		- composer require symfony/rate-limiter
	- Installer le bundle KnpPaginatorBundle pour la pagination
		- composer require knplabs/knp-paginator-bundle
	- Installer le bundle google mailer pour envoyer les e-mails via Gmail
		- composer require symfony/google-mailer
	- Installer le bundle pour la réeinitialisation de mot de passe
		- composer require symfonycasts/reset-password-bundle
		- php bin/console make:reset-password
les identifiants pour le site:
	Compte admin:
		email:
		password:
	Compte Employee:
		email:
		password:
	Compte Client:
		email:
		password:
