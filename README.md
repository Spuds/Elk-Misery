## Misery

### License
This addon is released under a MPL V1.1 license, a copy of it with its provisions is included with the package.

### Introduction
This addon allows you to target specific users and give them the special gift of "misery" until they chill out or leave your community.
It can be used as

* As an alternative to banning or deleting users from a community.
* As a means by which to punish members of your community.
* To delight in the suffering of others.

### Features
The aim of misery is to be not traceable by users that are on the list, so misery actions should be sufficiently subtle to avoid suspicion. It provides that selected users may experience various "errors" interrupting their site experience.

The following page load misery can be applied:

* Delay: Creates a random-length delay, giving the appearance of a slow connection
* White screen: Presents the user with a white-screen
* Wrong page: Redirects to a defined URL instead of the page they requested
* Random HTTP errors: Presents error screens like 403 Access Denied, 404 Not Found, etc
* JS popup: Shows a user defined message (by default it says to enable cookies) that they must clear to continue
* Logout: Log the user out

Form misery (posting, PM, email) when applied will randomly result in the following errors

* They submit but it does not actually post
* The message subject is removed and they are warned that a subject is needed
* The message body is removed and they are warned that a message can't be left blank
* Session timeout error
* Already submitted error
* Post to long error

Feature disabled, blamed on server load, includes

* Search disabled
* New replies to your posts disabled
* Show this user's posts disabled
* Show unread posts since last visit disabled
* Show recent posts disabled

### How to Use
In your admin panel you will need to enable it, and set the various misery options in percent.  You will find the settings under Security and Moderation -> Misery