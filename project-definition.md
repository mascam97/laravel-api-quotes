# Project definition - Laravel8 API Quotes

## Goal

Define the business logic (high level logic) about the project to justify the API logic, tools, functionalities, etc.

## Client requirements

In the community "SocialMediaQuotes" the members share quotes about books, each member rates the quotes that was shared by a member.  

There is not an order about the quotes that belongs to a specific member, they use like a chat to publish it, and every member gives a like.

So, this community needs a system that allow the members upload "quotes", in order to other member can rate it with a qualification from 1-5

The community hires a company to develop the mobile or website, and this company requires your services as **Backend Developer** to build an API that allows:

1. Basic Register and Login system
2. Each user can upload quotes
3. Each user can rate any quote (owned are not included)
4. Each user can update and delete its quotes and rates
5. Each user can get other quotes and list users
6. When a quote is rated, the owner is notifiable

At the moment the community is from a city in a country, they are not planning to growth to other countries, but they want english and spanish support for the interfaces.

There are many groups in the community, so they are planning (in a distant future) to use a group to upload and rate quotes. (Polymorphic relationship)

They also have many great ideas, but now they want an initial product to deploy with the main requirements. (Versioning)

