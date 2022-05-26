const ratio = 0.2

var options = {
  root: null,
  rootMargin: '0px',
  threshold: ratio
}

const handleIntersect = function (entries, observer) {
	entries.forEach(entry => {
		//quand l'élément est visible
		if (entry.intersectionRatio > ratio) {
			entry.target.classList.add('reveal-visible');
			//désactiver l'observer pour ne pas répéter l'animation plusieurs fois
			observer.unobserve(entry.target)
		}
		/*si on souhaite relancer l'animation à chaque passage, on peut utiliser
		un else ici exécuter du code lorsque l'élément devient invisible*/
	})
}

var observer = new IntersectionObserver(handleIntersect, options);

observer.observe(document.querySelector('.reveal'));

//Si on souhaite utiliser la classe sur plusieurs élément
document.querySelectorAll('.reveal').forEach(function (r) {
	observer.observe(r);
})
