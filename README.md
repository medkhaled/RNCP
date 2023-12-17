Configuration de projet:
	- Mise en place de l'environnemet de développement(PHP>8,Symfony>6,SGBD)
	- Installer les fixtures (fausses données)
		- composer require --dev orm-fixtures
		- composer require fakerphp/faker
	- Installer  Rate Limiter component pour se protoger contre "brute force login attacks"
		- composer require symfony/rate-limiter
	- Installer le bundle KnpPaginatorBundle pour la pagination
		- composer require knplabs/knp-paginator-bundle
