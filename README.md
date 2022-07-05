# Welcome to Weave

This repo is an example Weave project using the following Adaptors:

Component | Adaptor
----------|--------
Configuration | Laminas Config
Error Handling | Whoops
DIC | Aura.Di
Middleware | Relay
Router | Aura.Router
PSR7 | Laminas Diactoros
Resolver | Weave

For more info about Weave, please see http://github.com/weavephp/weave

## Example structure
 * html/index.php - The starting point of all requests
 * config/development.json - An example config file with a single key
 * src/App.php - The main App class
 * src/Config.php - The Aura.Di config class
 * src/Middleware/UppercaseOwner.php - An example Middleware
 * src/Controller/Hello.php - An example Controller

## Demo environment

The example expects a php56 or php7x environment with apache. If you are comfortable with Docker then there's a docker-compose.yml config you can use with `docker-compose up` from the root folder of the demo. The docker environment will expose port 8085.

Browse to http://localhost:8085/ (for the docker environment) or whatever url you have configured for your environment and you should see the text `Hello, WORLD`. Any text you append to the url will replace `WORLD` so, for example, http://localhost:8085/wibble will show `Hello, WIBBLE`.

## Example walkthrough

The html/index.php file loads in the composer autoloader, creates a new instance of App and then calls the start() method on the instance, passing in a value for the environment and a path to the config. The start() method is provided by Weave. The environment string can be anything you want and the config path depends on which Adaptor you use for config loading. In this case the environment string is a Const set to 'development' and we are using Laminas Config so the path and the environment string are combined to load the config/development.json file.

The src/App.php file is the main App class. Notice that Weave is applied as a trait on the class. Weave provides a start() method and requires three other methods be defined - one for config loading, one for error handling and one for loading the dependency injection container. You could write these methods yourself but in this example we are using Adaptors to supply them.

* The config loading is handled with the Laminas Config Adaptor which is a trait on the App class.
* The error handling is via the Whoops error Adaptor which is, again, a trait on the App class.
* Loading of the Aura.Di DIC is via the Aura Container Adaptor which is also a trait on the App class.

Traits are well suited to this kind of use because you have full control over whether you apply them and they are very easy to adjust and override for your needs.

After the trait uses and the const definition, the first method in the App class is `provideContainerConfigs()`. This method is not a core method of Weave but is in fact required by the Aura Container Adaptor. The method accepts the environment string we discussed above as well as an array of config keys. Aura.Di is configured via config classes and this method is where you supply an array of those you need. You can supply class name strings or class instances (see the Aura.Di docs for info on how to use Aura.Di). Here we want to pass in the config array so we instantiate an instance of our Config class.

The DIC is where we control the other Adaptors we want to use so take a quick look at the src/Config.php file to see what's happening. There are 5 statements in the define() method that map a named Interface to a specific Adaptor class for each of the 5 interfaces we need Adaptors for. The first specifies we want to use Relay for our Middleware. The next three state we want to use Laminas Diactoros for our PSR7 implementation and the last of the 5 states we want to use Aura.Router for our routing. The last statement says that when we create an instance of the Hello Controller, pass the string from the development.json config file as the 'message' parameter on the constructor. We'll come back to that later.

Back in the src/App.php file, the next method is `provideMiddlewarePipeline()`. This method is where we setup our Middleware pipelines (as you can see, the method is imaginatively named!). Middleware pipelines are arrays of either class instances, callables, closures or dispatch strings. We'll cover what dispatch strings are all about a bit later. Your App can, and often will, have multiple Middleware pipelines. The default pipeline, with a name of null, is the starting pipeline in all cases. Here, our default pipeline does nothing other than call the router. The other pipeline, called `uppercaseOwner` first calls the UppercaseOwner middleware, and then a class called Dispatch. We'll come back to this pipeline in a bit.

The next, and last, method in the App class is `provideRouteConfiguration()`. This method isn't a Weave core method either. It's specific to our Router (Aura.Router). Different Routers are likely to have a similar method but with different parameters. This is where we set up routes for our router. See the docs for Aura.Router for details on how to configure it. Put simply, we configure a single GET route on '/' with an optional value we call `owner` that has a default value of `World`. The odd-looking `['uppercaseOwner|' , Controller\Hello::class . '->hello']` (which is also allowed to be a string such as `'uppercaseOwner|Controller\Hello::class . '->hello'`) is the dispatch chain.

A dispatch chain can refer to:
* an invokable class: `\App\Wibble`
* a static method on a class: `\App\Wibble::methodName`
* a method on an instance of a class: `\App\Wibble->methodName`
* a named Middleware pipeline: `pipelineName|`
* a chain of pipeline names, possibly ending in any of the other options: `pipe1|pipe2|\App\Wibble`

In the case of our single route, it specifies to run the pipeline called `uppercaseOwner` and then do something with the `\App\Controller\Hello->hello` bit. If you look back at the `uppercaseOwner` pipeline you will remember that the last call in the stack is to a Middleware called `\Weave\Middleware\Dispatch` and it is this Middleware that consumes and processes the `\App\Controller\Hello->hello` bit by creating an instance of `\App\Controller\Hello` and calling the `hello` method on it.

Often it is convenient to use the string representation for a dispatch chain. However, when the chain is dynamically created or if the final dispatch entry is a closure then the array syntax is better. Internally, string chains are converted to array style.

Looking next at src/Middleware/UppercaseOwner.php, this is a very simple middleware class that takes the `owner` attribute from our route and makes it uppercase.

Lastly, looking at src/Controller/Hello.php we see a class that accepts the key from the config file (remember the last statement in the src/Config.php class we used to set up the DIC earlier?) and the 'owner' attribute from the route and which returns a PSR7 Response setup as plain text and with the message we see in the browser.

In order to show the message then, the flow is:
1. index.php
2. App->start
3. The default middleware pipe
4. The router
5. The uppercaseOwner middleware pipe
6. The UppercaseOwner middleware
7. The Dispatch middleware
8. The Hello Controller

## Plug And Play

Take a look at the other examples at github.com/weavephp to see the same sample app using different Adaptors.