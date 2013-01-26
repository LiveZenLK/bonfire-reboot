# Bonfire Rebooted

Bonfire is a Kickstarter for CodeIgniter-based web applications that provides much of the functionality that you need to start off new web applications so that you don't have to code it all up yourself. It is currently at version 0.6, with a pretty stable 0.7 in the develop branch, but, after working several large projects recently built off it, I realized it needed some TLC to become what it could truly become. This is a near complete rewrite of the project in a much more friendly and flexible way.

## What to expect?

In doing a rewrite I have a few goals in mind:

- The core must be easy for an experienced CI dev to jump into. This means that it can be used in the typical CodeIgniter fashion of working directly in the application folder without any changes. Then, they can slowly start taking advantage of the expanded pieces of the software as they become familiar with it. As it is currently, the learning curve is too steep and confusing for some. Let's fix that.
- Everything should be streamlined. This means more intuitive function names, less functionality in some places in the favor of ease of use and performance. This is especially true where Permissions are concerned.
- While things are being simplified, more functionality should be added in certain places, like a better navigation system built in and more flexible routing.
- Testing and documentation should take a higher priority. I'm trying to do these as I go. When it comes to testing, it turns out that CodeIgniter doesn't like being tested. This will probably mean more behaviour-driven testing, though not with a strict BDD framework. I think SimpleTest will work fine here.
- We'll keep the core nice and lean with only the most common features. This will mean that some modules you used to have will no longer live in core. They will, however, live on as addons that can be easily dropped into place.
- The UI should be geared as much as possible to the end-user experience, not the developers. This works pretty good at the moment, but there are a few places where it's obvious that it is intended for a developer.
- The command line is our friend. I plan on incorporating a good CLI experience building and testing code. This makes it simpler to work with as part of a build process.