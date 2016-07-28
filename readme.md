Llama Commander
---

[![Build Status](https://travis-ci.org/beryllium/llama.svg)](https://travis-ci.org/beryllium/llama)

Llama Commander lets you use anonymous functions as console commands via the
Symfony Console Component.

Why does it exist?
---

Llama Commander allows developers to define Symfony Console Commands inline
instead of having to create a class for each command. When developers are using
the Console in a microframework or no-framework context, they may benefit from
this flexibility.

What is the spec?
---

The class **LlamaCommand** takes the following parameters:

* **$name**: The name for the command (optional on instantiation, but must be set in the constructor or via the ->setName() or ->configure() methods before the command can be invoked)
* **$configurator**: An optional callable function to configure the command. This can be used to set the Description, Options, and Arguments for the console command.
* **$executor**: The core logic for the console command. This callable will receive the input and return the output at the core of the console command being written.
* **$interactor**: An optional callable that defines user interaction logic.
* **$initializer**: An optional callable that initializes parameters before interaction and execution.

How do I use it?
---

When you initialize a new ```LlamaCommand```, you pass it at minimum a name (string) and an
"Executor" lambda (callable) that determines what the command does. You can also pass it a
Configurator, an Initializer, or an Interactor.

    ```php
    $app     = new Application;        // Silex application
    $console = new ConsoleApplication; // Symfony console component

    /* ... extra application setup, defining the pheanstalk service, etc ... */

    $console->add(new Beryllium\Llama\LlamaCommand(
        'queue:listen',     // Start a queue listener
        null,               // No configuration at this time
        function ($input, $output) use ($app, $console) {
            do {
                $job = $app['pheanstalk']
                  ->watch('testtube')
                  ->ignore('default')
                  ->reserve();

                $output->writeln('Raw data: ' . $job->getData());
                $app['pheanstalk']->delete($job);
            } while (strtolower($job->getData()) !== 'halt');
        }
    ));

    $console->run();
    ```

If your command needs to have options or arguments, you can specify an anonymous
function for that:

    ```php
    $console->add(new Beryllium\Llama\LlamaCommand(
        'queue:listen',     // Start a queue listener
        function ($config) use ($app, $console) {
            $config->setDescription('Listen for stuff to do')
                   ->addArgument(
                       'items',
                       InputArgument::OPTIONAL,
                       'How much stuff to listen for'
                   );
        },
        function ($input, $output) use ($app, $console) {
            // ... command code goes here
        }
    ));
    ```

If you want your code to be more precise, you can also typehint the main lambda.
This will help your IDE give you hints about how to interact with $input and
$output:

    ```php
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;

    $app     = new Application;        // Silex application
    $console = new ConsoleApplication; // Symfony console component

    /* ... extra application setup ... */

    $console->add(new Beryllium\Llama\LlamaCommand(
        'queue:listen',     // Start a queue listener
        null,               // No configuration at this time
        function (InputInterface $input, OutputInterface $output) use ($app, $console) {
            // ... now $input and $output are type-hinted
        }
    ));

    $console->run();
    ```

Combining the above examples, you would register the command with the console application like so:

    ```php
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;

    $app     = new Application;        // Silex application
    $console = new ConsoleApplication; // Symfony console component

    /* ... extra application setup, defining the pheanstalk service, etc ... */

    $console->add(new Beryllium\Llama\LlamaCommand(
        'queue:listen',
        function ($config) use ($app, $console) {
            $config->setDescription('Listen for stuff to do')
                   ->addArgument(
                       'items',
                       InputArgument::OPTIONAL,
                       'How much stuff to listen for'
                   );
        },
        function (InputInterface $input, OutputInterface $output) use ($app, $console) {
            $pheanstalk = $app['pheanstalk'];

            do {
                $job = $pheanstalk
                  ->watch('testtube')
                  ->ignore('default')
                  ->reserve();

                $output->writeln('Raw data: ' . $job->getData());
                $pheanstalk->delete($job);
            } while (strtolower($job->getData()) !== 'halt');
        }
    ));

    $console->run();
    ```

And that's all for now. If you would like to learn more about how to use the Symfony Console Component, the Symfony website has a [very helpful documentation page about it](http://symfony.com/doc/current/components/console/introduction.html).
