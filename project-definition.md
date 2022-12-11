# Laravel API Quotes - Project definition 

__Business logic to justify the tools and functionalities__

## Problem to solve

The community "SocialMediaQuotes" have members who like reading, talk about books and share information.

They have a social media group, where each member shares quotes and every member rates them with a like.

The community has many problems to define the best quote and organize the information. 

### Solution

A software product where the members can see the information and interact with.

## Authentication

As User, I can register my information to login and use the software

**TODO**: As User, I can verify my email after the registration

**TODO**: As User, I can update my data and password for security reasons

## Authorization

As User, I cannot update and delete data that is not mine for security reasons

## Quotes CRUD

As User, I can create, read, update and delete quotes to show them in the community

**TODO (External API)**: As User, I want to see quotes from famous people to inspire me

### State pattern

**TODO**: As User, I can see only the quotes as `PUBLISHED` from other users

As User, I can set a quotes as `DRAFTED` to save it, and when it is ready I can set as `PUBLISHED`

**TODO**: As User, I can report an unappropriated quotes to set as `BANNED`

## Quotes Rating

As User, I can rate (from 1 to 5) and unrate quotes of the community to point out the best ones

**TODO**: As User, I can make a comment about the quotes to justify my rating

### Polymorphic relationship

In a future where the system allows creating groups, as **group**, I can rate quotes as a normal user

## Notifications

As User, I received a welcome mail when I register myself

As User, I can receive a newsletter to know news about the community and the quotes 

**TODO**: As User, I can subscribe and unsubscribe for the newsletter

As quote owner, I received a notification via email when the quote is rated

**TODO (Settings and Database notifications)**: As User, I can decide if I want to receive notification via email o via database

## Localization

As User from Latam, I can change the language to spanish to have a better experience

## Administrator

**TODO**: As Administrator I can see and delete users from the system for security reasons
