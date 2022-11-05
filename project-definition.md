# Project definition - Laravel API Quotes

## Goal

Define the business logic about the project to justify the API logic, tools and functionalities.

## Client requirements

In the community "SocialMediaQuotes" the members share quotes about books, each member rates the quotes with a like.  

So, this community needs a system that allow the members upload "quotes", in order to other member can rate it with a qualification from 1-5

The community hires a company to develop the mobile or website, and this company requires your services as **Backend Developer** to build an API that allows:

1. Basic Register and Login system
2. Each user can create quotes
3. Each user can rate any quote
4. Each user can update and delete its quotes and rates
5. Each user can see other quotes and users
6. When a quote is rated, the owner is notifiable

### Extra features

- The community is from the USA, they are not planning to growth to other countries, but they want english and spanish support
- There are many groups in the community, so they are planning to use a group to upload and rate quotes (Polymorphic relationship)
- They also have many great ideas, but now they want an initial product with the main requirements. (Versioning)
- There are some rules about quotes, someone can create one but not to publish, if a quote is published it cannot be updated and if it has many reports it becomes banned. (State pattern)

