all: clone extract change-namespace

clean:
	rm -rf api-platform-core
	rm -rf src/FilterValidator/*

clone:
	git clone git@github.com:jdeniau/api-platform-core.git api-platform-core --branch jd-feat-moreQueryParameterValidation

extract:
	cd api-platform-core; \
	git remote add upstream git@github.com:api-platform/core; \
	git fetch upstream; \
	git diff --name-only --diff-filter=d upstream/master..HEAD src | grep -v '\.xml' | xargs -I '{}' cp --parents '{}' ../src/FilterValidator/
	mv src/FilterValidator/src/* src/FilterValidator
	rmdir src/FilterValidator/src/

change-namespace:
	find src -type f -name '*.php' -exec sed -i 's/namespace ApiPlatform/namespace ApiPlatformFilterValidator\\ApiPlatform/g' {} \;
	find src -type f -name '*.php' -exec sed -i 's/use ApiPlatform/use ApiPlatformFilterValidator\\ApiPlatform/g' {} \;
