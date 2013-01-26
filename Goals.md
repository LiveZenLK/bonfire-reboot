# Bonfire 1.0 Goals

The goal of the final Bonfire 1.0 release is to provide a flexible, powerful, and as simple as possible kickstart for applications. The current release is growing up too fast and becoming too bloated and difficult to maintain. In a 1.0 release, we need to streamline things. The new goals are: 

- A leaner package that includes only what is necessary. The things that are not necessary can be included in easy-to-download modules, but are not shipped with the core. Uses Composer for managing addons.
- The UI needs to be geared toward end users, and not toward developers as much as it has been
- Re-written HMVC to allow us to take advantage of every part of a module and get even more things out of the way of the application space and into Bonfire core. 
- Architecture should be re-factored to remain as elegant as possible, without breaking too many areas of the current API.
- Re-factored Migrations to extend CodeIgniter’s so we take advantage of their stuff as they add it.
- Fully documented
- Large test coverage (testing is at it’s core!)
- Use Exception Handling throughout to provide a better ability to deal with the error types out there.

## Core Modules
The following modules are the ones that should be at the core and shipped with the default:

- **Users** is one of the core things that draws people to it. It should have all of the features that one would need, with the exception of Social Logins. But let’s include the AccountChooser-type of login.
- **Roles & Permissions** should also be included by default, but we need to streamline things. Remove uneccessary permissions, and simply them to be mostly .View and .Manage
- **Menus** A new menu system to replace the existing one. While the current concept of Contexts can be valuable, I think it does more harm than good and is too restrictive. We should make them a second-class citizen where it still works, but isn’t mandatory. Includes a UI
- **Settings** that is much more flexible and can include things from other modules into the main UI easily.
-  **Cronjob** implementation that is more user-focused than developer focused.
- **Activities** with a much-nicer UI :)
- **System Events** system that allows hooks into the email system.
- **Emailer**
- 