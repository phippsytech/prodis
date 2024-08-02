To Set up the CLI

Install Symfony console.
```composer require symfony/console```


Making the Script Executable
To make the manage.php script executable, run the following command from your terminal:

```chmod +x cli/manage.php```

## Running the CLI Script
You can now run your CLI script from the terminal:


```./cli/manage.php app:say-hello```

Summary
cli/: Directory for CLI scripts to manage your application.
manage.php: Entry point for your CLI application using Symfony Console.
Commands: Define your commands under app/Console/Commands/.
By following this structure, you can easily manage your application with custom CLI commands


Steps to Set Up an Alias for Your CLI Script
Create a Symbolic Link: Create a symbolic link to your cli/manage.php script in a directory that is in your PATH. Common directories for this are /usr/local/bin or ~/bin.

Ensure the Script is Executable: Make sure your manage.php script is executable.

Verify and Use the Alias: Use the alias to run your CLI commands.

Detailed Steps
1. Make the Script Executable
Ensure manage.php is executable:


```chmod +x cli/manage.php```
2. Create a Symbolic Link
Create a symbolic link in a directory that is in your PATH. Here, we'll use /usr/local/bin:


```sudo ln -s /path/to/your/project/cli/manage.php /usr/local/bin/ptcli```
Replace /path/to/your/project with the actual path to your project.

If you prefer not to use sudo, you can use a directory in your home directory:


```mkdir -p ~/bin
ln -s /path/to/your/project/cli/manage.php ~/bin/ptcli```
Then, add ~/bin to your PATH if it's not already there by adding the following line to your .bashrc, .zshrc, or appropriate shell configuration file:


```
export PATH="$HOME/bin:$PATH"
```
After editing the file, apply the changes:


source ~/.bashrc  # or source ~/.zshrc
3. Verify and Use the Alias
Now you can use ptcli to run your CLI commands:


```ptcli app:say-hello```

Summary
Executable Script: Ensure your manage.php script is executable.
Symbolic Link: Create a symbolic link to the script in a directory that is in your PATH.
Use the Alias: Run your CLI commands using the alias.