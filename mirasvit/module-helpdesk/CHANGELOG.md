## 1.3.6
*(2025-03-04)*

#### Fixed
* Incorrect imageindex in the images preview array
* Changed Contact Us styles
---

## 1.3.5
*(2025-02-27)*

#### Fixed
* Passing null to parameter of type string is deprecated for emails with empty subject
* Ticket fetched from the email are not parsed correctly

---

## 1.3.4
*(2025-02-18)*

#### Improvements
* Attachment image preview

---

## 1.3.3
*(2025-02-10)*

#### Fixed
* ticket_template is not defined in when send ticket email
* Refused to execute inline script because it violates CSP

---

## 1.3.1
*(2025-02-04)*

#### Fixed
* Added email variable is_message_internal

#### Improvements
* Added doc links to the admin menu pages

---

## 1.3.0
*(2025-01-22)*

#### Improvements
* Clear outdated tables data by cron
* Configuration refactoring

#### Fixed
* Quotes replaced with HTML code in the ticket messages
* Message is cut when emojies are added from the admin to the ticket message

---

## 1.2.48
*(2025-01-09)*

#### Fixed
* Undefined property: Mirasvit\Helpdesk\Block\Email\Satisfaction::$escapeUrl

---

## 1.2.47
*(2025-01-08)*

#### Fixed
* Tags displayed in order label in customer account

---

## 1.2.46
*(2025-01-06)*

#### Fixed
* Email text cut after emoji in Outlook email

---

## 1.2.45
*(2024-12-12)*

#### Fixed
* Removed deprecated column "Status Sort Order" from admin ticket grid
* Deprecated functionality passing null to parameter 1 in Helpdesk History
* Non-existent attachment is aaded to user notify email
* Display Order Frontend Status Label in tickets

---

## 1.2.44
*(2024-11-13)*

#### Fixed
* Column user_id in where clause is ambiguous in ticket activity in admin ticket grid

---

## 1.2.43
*(2024-11-06)*

#### Improvements
* Ability to attach several attachments in a bulk

#### Fixed
* Orders related to different websites are loaded in ticket customr section if customer is registered on different websites with same email 
* Admin ticket grid sorting column does not work on fulltext search
* Last public reply does not added to the ticket history if the current reply type is internal
* "Mail was sent" column is removed from the ticket grid due to possible performance

---

## 1.2.42
*(2024-10-17)*

#### Fixed
* Recaptcha compatibility with 2.4.3-p3 
* Outlook Mac emails parsed incorrectly

---

## 1.2.41
*(2024-10-07)*

#### Fixed
* Messages sent from outlook are not formatted
* Order links for no order tickets in admin grid
* Email text cut if emoji is added to the email from outlook
* Wrong Department name on ticket view page on in customer account if admin is assigned to several storeview departments
* Contact form name field validation

---

## 1.2.40
*(2024-08-15)*

#### Fixed
* Deprecated Functionality: explode(): Passing null to parameter #2 of type string is deprecated

---

## 1.2.39
*(2024-07-31)*

#### Fixed
* Registry key is_ticked_created already exists

---

## 1.2.38
*(2024-07-26)*

#### Fixed
* reCaptcha does not work in Hyva

---

## 1.2.37
*(2024-07-23)*

#### Improvements
* Added indexes to the helpdesk report tables
* Added "Order" column to the ticket grid in the admin panel
* Added cronjob & console command to remove old desktop notifications

#### Fixed
* Removed legasy DOMSubtreeModified event type
* Fallback to JQueryUI Compat activated. Your store is missing a dependency for a jQueryUI widget
* Desktop Notifications about new ticket arrival sent messages about new message arrival

---

## 1.2.36
*(2024-06-20)*

#### Fixed
* Argument #1 ($array) must be of type array, string given in TicketDistributionDataBlock

---

## 1.2.35
*(2024-06-13)*

#### Fixed
* Admin ticket grid search performance

---

## 1.2.34
*(2024-05-30)*

#### Fixed
* Offline chat messages does not converted to tickets

---

## 1.2.33
*(2024-05-24)*

#### Improvements
* Helpdesk widget GoogleRecaptcha compatibility

#### Fixed
* Prevent undelivered message autoresponce loop
* Dots were not allowed in the contact us form
* Google ReCaptcha did not validate contact forms

---

## 1.2.32
*(2024-02-12)*

#### Fixed
* Interface "Zend\Stdlib\JsonSerializable" not found

---

## 1.2.30
*(2023-12-13)*

#### Fixed
* Show Helpdesk Gatway error message on each page load in the backend & Added message "Authentication failed" for the Gateways

---

## 1.2.29
*(2023-12-12)*

#### Fixed
* Emails redirected to the Gateway from Outlook are not fetched

---

#### Improvements
* Added "Remote Ip" column to the Satisfaction grid in the admin panel
* 
---

## 1.2.28
*(2023-12-04)*

#### Fixed
* Failed to parse time string in Activity.php

---

## 1.2.27
*(2023-12-01)*

#### Fixed
* Activity Data Bar displaying is bound to the timezone 
* Exclude users not assigned to the Helpdesk Departments from Activity Data Bar

---

## 1.2.26
*(2023-11-23)*

#### Fixed
* Prevent infinity cycles for the Auto-Submitted emails

---

## 1.2.25
*(2023-11-06)*

#### Fixed
* Don't show [user_name] in email confirmation of newly created ticket

---

## 1.2.24
*(2023-11-01)*

#### Fixed
* Creation of dynamic property Mirasvit_Ddeboer_Imap_Message_Attachment::$description is deprecated
* Guest orders was not assigned to the ticket if the customer is registered in the store

#### Improvements
* Added User Activity bar to the ticket grid in the admin panel

---

## 1.2.23
*(2023-09-26)*

#### Fixed
* Helpdesk wrapper notification count rewrite magento wrapper notification counter

---

## 1.2.22
*(2023-08-28)*

#### Improvements
* Added option to auto-remove old ticket attachments

#### Fixed
* Incorrect use of label for=FORM_ELEMENT in contact form
* Wrong third-party email is set to the message if the last fetched "third party" email differs from the penultimate
* Incorrectly parced links if first level domain contains more than three symbols

---

## 1.2.21
*(2023-06-29)*

#### Improvements
* Parce URLs in ticket message posts to avoid adding excessive symbols like dot/comma/etc to the end of the urls and broke the url
* Compatibility with Magento246 SMTP
* Added script to migrate tickets data from Freshdesk

#### Fixed
* Deprecated Functionality: stripos(): Passing null to parameter #1 in module-price-permissions when create rule

---

## 1.2.20
*(2023-05-19)*

#### Improvements
* Show first and last ticket messages in preview in the admin ticket grid

#### Fixed
* Rule notification sent email to Cc when customer should not be notified

---

## 1.2.19
*(2023-04-20)*

#### Fixed
* Added third-party email to display for third-party message on the ticket view page in admin
* Store-view Status & Priority names in email notification
* Third party email is not saved if the customer replies not from the email he was emailed to
* Deprecated functionality: htmlspecialchars() passing null to parameter #1 in Helper/StringUtil.php
* Save empty value in admin config multiselect fields

---

## 1.2.18
*(2023-02-21)*

#### Fixed
* Remove non-char symbols from the end of the urls in email messages
* Deprecated functionality passing null to parameter in geoip_time_zone_by_country_and_region Timezone.php
* Column in where clause is ambiguous when filter by department or priority in admin ticket grid
* Empty Satisfaction email notification

---

## 1.2.17
*(2023-02-10)*

#### Fixed
* Rates and history blocks are not added to notification email template

---

## 1.2.16
*(2023-02-08)*

#### Fixed
* Warning: Attempt to read property "Mailbox" on bool 
* Store-view Department name in email notification
* Attachment is not fetched due to specialchars in attribute name

---

## 1.2.15
*(2023-02-01)*

#### Fixed
* Removed auto-reply header from error headers to avoid ignoring emails fetching, due to outlook & Gmail adding it to the regular emails
* Added Microsoft Ips to block auto satisfation rates

#### Improvements
* Compatibility with M2.4.6

---

## 1.2.14
*(2023-01-23)*

#### Fixed
* Deprecated Functionality: explode(): Passing null to parameter
* Deprecated functionality passing null to parameter in geoip_time_zone_by_country_and_region Timezone.php
* Failed to parse time string when save working hours for EU locales, for active_from, active_to fields
* Shorter name for 'Add Ticket ti the Order' button on the admin order view page
* Shorter button name for the customer edit admin page
* Call to a member function getAddress() on array Helpdesk/Helper/Fetch.php
* Added new option "sender template"

---

## 1.2.13
*(2022-11-21)*

#### Fixed
* Notice: Undefined index: created_at in the ticket grid for nonexistent messsages
* Parse attachment
* record.isQuickView is not a function in the ticket grid in admin
* Quick Responces did not work in wysiwyg in m2.4.5

---

## 1.2.12
*(2022-10-28)*

#### Fixed
* As of Magento 2.4.4 info & critical methods are added instead of addInfo & addCritical
* Fatal "Allowed memory size" for quick-view message in the tickets grid in the admin
* Allowed memory limit when delete ticket with thousands of messages

---

## 1.2.10
*(2022-10-06)*

#### Fixed
* Cc & Bcc email addresses weren't validated if added more than one address
* Deprecated Functionality: IntlDateFormatter::parse(): Passing null to parameter #1

---

## 1.2.9
*(2022-09-12)*

#### Fixed
* Email validation
* strpos() Passing null to parameter #1

---

## 1.2.8
*(2022-08-23)*

#### Fixed
* Quickbar filters now show only Inbox tickets
* Status and priority are set from Workflow Rules if set in the ticket manually in the admin
* If there is a great number of the non-archived tickets admin panel returns error 500 due to the toolbar collection limit

---

## 1.2.7
*(2022-08-10)*

#### Fixed
* Notification icon shows for users without permissions.
* Compatibility with Magento 2.4.5 

---

## 1.2.6
*(2022-08-02)*

#### Fixed
* php8 compatibility quick response 

---

## 1.2.5
*(2022-08-01)*

#### Fixed
* php8 compatibility quick response 

---

## 1.2.4
*(2022-07-24)*

#### Fixed
* php8 compatibility issues 
* Department, Priority, Status names are not added in the email notification 

#### Improvements
*  Added option to enable/disable quickDataBar

---

## 1.2.3
*(2022-07-21)*

#### Fixed
* Required checkbox is not validated for the new tickets 
* Segoe UI Emoji are fetched as questionmarks 
* Passing null to parameter #2 () when access Gateway Debug 

---

## 1.2.2
*(2022-07-04)*

#### Improvements
* Added quickbar

#### Fixed
* Forwarded email messages fetched empty

---

## 1.2.1
*(2022-06-20)*

#### Improvements
* Remove db_schema_whitelist.json
* Added option 'Add customers Cc emails to the ticket'
* Added headers

---

## 1.2.0
*(2022-05-24)*

#### Improvements
* Migrate to declarative schema
* Added email variables in the user signature

---


## 1.1.158
*(2022-05-17)*

#### Improvements
* update mirasvit/module-report dependency

---



# 1.1.157
*(2022-04-18)*

#### Improvements
* PHP8 compatibility

#### Fixed
* Helpdesk user signature unset when user logged in the backend

---

# 1.1.156
*(2022-02-07)*

#### Improvements
* Integration with Magento 2 Custom Form Builder

#### Fixed
* Workflow rule event 'new reply from staff' doesn't work for internal messages to third party
* Schedule status block is loaded when it disabled from admin

---

# 1.1.155
*(2022-01-19)*

#### Improvements
* Added option to set statuses which lock ticket

#### Fixed
* Empty spam tickets created from the feedback tab

---

# 1.1.154
*(2021-11-23)*

#### Fixed
* Unable to add bmp & xlsx attachment to the tickets
* Added button 'Create New Ticket' to the ticket admin form

---

# 1.1.153
*(2021-07-27)*

#### Fixed
* Duplicate entry ON DUPLICATE KEY UPDATE in report

---

# 1.1.151
*(2021-07-19)*

#### Fixed
* Unable to add csv attachment to the tickets
* Integrity constraint violation in reports
* Message parser

---

# 1.1.149
*(2021-05-31)*

#### Fixed
* Hours not displayed according to server locale in reports
* Helpdesk does not ignore auto response emails
* Made customer Email insensitive in customer_summary in the admin view ticket
* Emoji and inline images parsing

---

# 1.1.148
*(2021-03-01)*

#### Fixed
* Sender name in department is required field
* Priority is not displayed in the ticket summary on the frontend->ticket view
* Added option Settings->Customer Satisfaction Survey->Show survey results in the frontend ticket history
* Added Max file size for uploading to the ticket create form and to the Contact Us page
* Changed filter for dates in the ticket grid according to the locale format
* Search of the customer by name(instead of the email) in the new ticket make the ticket unassigned to customer

#### Improvements
* Added customer name to the tickets with Guest orders
---

# 1.1.147
*(2021-01-26)*

#### Fixed
* Cannot add or update a child row priority_id when customer adds a message or if message is fetched from email if priority is deleted

---

## 1.1.146
*(2021-01-21)*

#### Fixed
* Issue when values in the workflow rules condition overwrite on save
* Internal notes are sent to Cc if the admin adds an internal message to the ticket that is assigned to another admin

---

## 1.1.145
*(2021-01-15)*

#### Fixed
* Conditions are not saved in Workflow Rules if they contain selection from the Custom field dropdown
* Broken headers in the emails and backslashes appearing due to unresolved symbols
* Added Arabian translation file

---

## 1.1.144
*(2020-12-15)*

#### Fixed
* Error "Uncaught Error: Call to a member function getName() on null in Helpdesk/Helper/History.php"
* Satisfaction rate rounding
* Saving of the FollowUp email addresses

---

## 1.1.142
*(2020-12-07)*

#### Fixed
* Cron error (mailAddress) is not a valid hostname for the email address for the FollowUp addresses with added spaces
* Workflow rules don't work when customer replies(VariableObject->getData on boolean)
* Added full version of polish translation

---

## 1.1.141
*(2020-11-11)*

#### Fixed
* Recaptcha m2.4.1
* Call to a member function isThirdPartyPublic() on boolean
* Migrating custom email templates

---

## 1.1.140
*(2020-10-30)*

#### Fixed
* Third party email added as Public after the previous public email reply

#### Improvements
* Added support of composer 2.0

---

## 1.1.139
*(2020-10-20)*

#### Fixed
* Parsing ebay members emails
* Selection of customer's orders on the frontend
* Column 'created_at' & 'user_id' in where clause is ambiguous Satisfaction grid filters
* Satisfaction rate IDs Bad and Great are mixed up in the reports and displayed vise versa
* Unstable quick response text appearing when option 'Use WYSIWYG Editor in backend' is active

---

## 1.1.138
*(2020-09-30)*

#### Fixed
* Styles for backend MaxFileSize
* Misspellings

---

## 1.1.137
*(2020-09-09)*

#### Improvements
* Added ability to rate ticket messages on the ticket view page

#### Fixed
* Added maximum allowed size attachment for ticket edit form
* Validation of the custom fields required for admin

---

## 1.1.136
*(2020-09-03)*

#### Improvements
* Added Quick Response suggestions

---

## 1.1.135
*(2020-09-02)*

#### Fixed
* Fixed Fatal error: Allowed memory size for the next/Previous buttons collection loading
* Replace See all tickets link to the top of the tickets list in dropdown
* Fixed the password is lost while saving gateway
* Email notification is sent to Cc with internal message to third party

---


## 1.1.134
*(2020-08-13)*

#### Fixed
* JS error when js template variables used in the text

#### Improvements
* Added filter for Last Replier field in the backend ticket grid


---

## 1.1.133
*(2020-07-29)*

#### Improvements
* Support of Magento 2.4

---

### 1.1.132
*(2020-07-23)*

#### Fixed
* Search filtered mass action issue
* User name displaying in customer account with option Sign staff replies Using Department Names

---

### 1.1.131
*(2020-07-06)*

#### Fixed
* Assign ticket to guest order
* Autofill of customer and email in contact us widget
* Definition of the last message for the internal note
* Timezone conversion for the field "Execute At" of "Follow Up"
* Sending of internal message for the 3rd party email
* Issue when field "Execute At" of "Follow Up" updated on ticket save
* Using regular expressions in the spam patterns

---

### 1.1.130
*(2020-05-27)*

#### Fixed
* Insert quick responce content at the current caret position in WYSIWYG

---

### 1.1.129
*(2020-05-15)*

#### Fixed
* Display of signature with styles
* Logging of follow up changes
* Follow up email sending

---

### 1.1.128
*(2020-05-14)*

#### Fixed
* Added index to the message_id fild in the attachment table
* Error "DateInterval::__construct(): Unknown or bad format (PD)" in working hours
* Added permission denied exeption for the user who is not assigned to any department
* Issue when administrator user is removed from department during user update data
* ReCaptcha for Helpdesk widget
* Styles for Thunderbird Mail
* Definition of the last message for variable var ticket.getLastMessageHtmlText()|raw

---

### 1.1.127
*(2020-04-15)*

#### Fixed
* Error "You cannot proceed with such operation, your reCaptcha reputation is too low." on satisfaction page
* Parsing of inline attachments
* Field description in the Feedback tab

---

### 1.1.126
*(2020-03-27)*

#### Fixed
* Display of message about success ticket creation in Contact Us form https://3.basecamp.com/4292992/buckets/14246559/todos/2530293296
* Tickets assigning to the current user during the ticket creation
* Issue "Help Desk MX popup not closing when background is clicked"

---

### 1.1.125
*(2020-03-19)*

#### Fixed
* Styles for tickets grid in customer account
* Remove emoji parsing for "quoted-printable" emails. https://3.basecamp.com/4292992/buckets/14246559/todos/2490106723

#### Improvement
* Added field to create customer note
* Moved user signature from email to message

---

### 1.1.123
*(2020-03-10)*

#### Fixed
* Error "Type Error occurred when creating object: MSP\ReCaptcha\Model\Provider\Failure\ObserverRedirectFailure"

---

### 1.1.122
*(2020-02-26)*

#### Fixed
* Display of custom field when option "Show in create ticket form" disabled

---

## 1.1.119
*(2020-02-25)*

#### Fixed
* Validation messages on contact us page https://3.basecamp.com/4292992/buckets/14246559/todos/2427320097
* Parsing of attachments (affects from 1.1.118) https://3.basecamp.com/4292992/buckets/14246946/todos/2441599106

#### Improvement
* Allowed to use string names of date formats in the email variables var ticket.getCreatedAtFormated($format) and var ticket.getUpdatedAtFormated($format)

---

## 1.1.118
*(2020-02-18)*

#### Fixed
* Parsing of emails with emoji (remove emoji) https://3.basecamp.com/4292992/buckets/14246559/todos/2409389597
* Error "PHP Fatal error: Uncaught Error: Call to a member function getFieldByCode() on null Mirasvit/Helpdesk/Controller/Adminhtml/Field/Save.php"

#### Improvement
* Added console commands:
  - mirasvit:helpdesk:is-locked - Show is helpdesk job locked
  - mirasvit:helpdesk:unlock - Force to unlock helpdesk job
https://3.basecamp.com/4292992/buckets/14246559/todos/2353803811

---

## 1.1.117
*(2020-02-11)*

#### Fixed
* Display of helpdesk feedback tab on product page for m2.3.4 https://3.basecamp.com/4292992/buckets/14246559/todos/2401574392
* Error "PHP Fatal error: Uncaught Error: Call to a member function getSenderNameOrEmail() on null in Helpdesk/Helper/History.php" https://3.basecamp.com/4292992/buckets/14246559/todos/2373178552
* Translation of email subject https://3.basecamp.com/4292992/buckets/14246559/todos/2354005437
* Field "Reply To" contains wrong data https://3.basecamp.com/4292992/buckets/14246559/todos/2343248097
* Draft errors on the backend ticket edit page

---

## 1.1.115
*(2020-01-17)*

#### Fixed
* Wrong order assigning to the ticket (from email subject)
* Sending emails to bcc and cc in m2.3.3
* Regular expression for parse order ID from email subject

---

## 1.1.113
*(2019-12-24)*

#### Fixed
* Sending of emails for customers with non-ASCII chars in the Department name
* Compatibility with the Knowledge Base extension (since version 1.1.112)

---

## 1.1.112
*(2019-12-13)*

#### Fixed
* Issue "Unable to assign order to guest customer"
* Change logging of ticket history

---

## 1.1.111
*(2019-12-05)*

#### Fixed
* Label for the ticket tab "Other Tickets" for m2.3.3
* Multi emails in "Send survey results to emails" for m2.3.3
* Dates for tickets and messages in migration process

#### Improvement
* Added option "Enable Google reCaptcha" and captcha to popup form
* Show customer local time

---

## 1.1.110
*(2019-11-28)*

#### Fixed
* Sending of workflow rules' emails for m2.3.3
* Sending of emails for customers with non-ASCII chars in the name
* Duplication of attachments for multi emails sending
* Report by user
* Error message for incorrect pattern

---

## 1.1.109
*(2019-11-19)*

#### Fixed
* Issue with parsing of attachments (since 1.1.108)

#### Improvement
* Allow rule events for tickets from archive

---

## 1.1.108
*(2019-11-11)*

#### Fixed
* Attachment sending in 2.3.3
* Remove email's line breaks from text part of the email
* Saving history for ticket mass-actions

---

## 1.1.107
*(2019-11-07)*

#### Fixed
* Error "Call to a member function getDepartmentIds() on null in Helpdesk/Model/ResourceModel/DesktopNotification/Collection.php"

---

## 1.1.106
*(2019-10-22)*

#### Fixed
* Order loading message on ticket pages

---

## 1.1.105
*(2019-10-17)*

#### Improvement
* Improve the way of orders loading on ticket pages

---

## 1.1.103
*(2019-10-11)*

#### Fixed
* minor fixes

---

## 1.1.102
*(2019-10-01)*

#### Fixed
* Compatibility with Magento 2.3.2-p1

---

## 1.1.101
*(2019-09-06)*

#### Fixed
* Minor changes

---

## 1.1.100
*(2019-09-06)*

#### Fixed
* Warning: file_get_contents No such file or directory

#### Improvement
* Added translation
* Added exception for deleted FS files

#### Features
* Attachments can be disabled

---

## 1.1.99
*(2019-08-23)*

#### Improvement
* added spam filters for contact us form

---

## 1.1.98
*(2019-08-13)*

#### Fixed
* Parsing of internal 3rd party messages
* Error "Unknown column 'main_table.id' in 'where clause' ..." in tickets grid on backend customer edit page

---

## 1.1.97
*(2019-08-12)*

#### Fixed
* Search results for storeviews in feedback tab

---

## 1.1.96
*(2019-07-31)*

#### Fixed
* Parsing of internal 3rd party messages
* Redirect on backend main page during message saving
* Paging for search results in backend tickets grid
* PHP Fatal error: Uncaught Error: Call to a member function isThirdPartyPublic() on boolean in Mirasvit/Helpdesk/Helper/Process.php
* Mobile styles for Helpdesk popup

---

## 1.1.95
*(2019-07-15)*

#### Fixed
* Display of errors in backend
* Issue when source link of the page does not submitted

---

## 1.1.94
*(2019-07-10)*

#### Fixed
* Address grid does not show in backend on customer edit page
* Allowing autofill the third party email input
* Issue when quick response does not insert in WYSIWYG Editor
* Display of errors in backend
* Parsing of internal 3rd party messages

---


## 1.1.93
*(2019-06-12)*

#### Fixed
* Compatibility with MSP_ReCaptcha

---


## 1.1.92
*(2019-06-05)*

#### Fixed
* Encryption for gateways passwords

---


## 1.1.91
*(2019-06-04)*

#### Fixed
* Translation in emails
* Encoding in attachments

---


## 1.1.89
*(2019-05-14)*

#### Fixed
* Display of two calendars for custom field of "date" type
* Issue when Feedback form overrides with cache

---


## 1.1.88
*(2019-05-13)*

#### Fixed
* ... Call to undefined method Mirasvit_Ddeboer_Imap_Message::getIsProcessed() in ... (v1.1.87 only)

---


## 1.1.87
*(2019-05-06)*

#### Fixed
* Sender name for m2.3.0
* Sending attachement error for m2.2.8
* Save unparsed emails

---


## 1.1.86
*(2019-04-19)*

#### Fixed
* compatibility with Ebizmarts_Mandrill for m2.3.1
* Warning: Division by zero in ... /Helpdesk/Block/Widget/Satisfaction.php

---


## 1.1.84
*(2019-03-15)*

#### Fixed
* Migration scripts

#### Improvement
* Added Satisfaction Widgets

---


## 1.1.83
*(2019-03-07)*

#### Fixed
* Helpdesk email sender

---


## 1.1.82
*(2019-02-28)*

#### Fixed
* Error when email has unsupported date format
* Empty "Auto-assign tickets to department" field raises error
* Error "The store that was requested wasn't found."
* Error "draftTicketId is not defined"
* Incorrect displaying of ticket history tags in email

---


## 1.1.80
*(2019-01-24)*

#### Fixed
* Notice: Undefined variable: fromEmail (v1.1.79 only)

---

## 1.1.79
*(2019-01-23)*

#### Fixed
* Issue with emogrifier
* Wysiwyg didn't work in M2.3
* Show correct email sender name in messages

---



### 1.1.78
*(2019-01-09)*

#### Fixed
* Added dependency for pelago/emogrefier. Fixed: Uncaught Error: Call to a member function appendChild() on null in..


## 1.1.77
*(2018-11-30)*

#### Fixed
* Error "Helpdesk email permission denied ..."

---

## 1.1.76
*(2018-11-29)*

#### Fixed
* Compatibility with Magento 2.3.0
* Error "error: error in [unknown object].fireEvent(): eventname: tinymceBeforeSetContent error message: content.gsub is not a function"

---

## 1.1.74
*(2018-11-19)*

#### Improvement
* Now unparseable tickets will log to file var/log/mirasvit/helpdesk_fatal.log

---

## 1.1.73
*(2018-11-16)*

#### Fixed
* Links in message contain unnecessary & nbsp; char
* Error "Notice: Undefined offset: 21 in Helpdesk/Helper/Field.php"

---

## 1.1.71
*(2018-11-15)*

#### Fixed
* Links in message contain unnecessary & nbsp; char

#### Features
* Added option that allows to log ticket deletion

---

## 1.1.70
*(2018-11-01)*

#### Fixed
* Remove Illegal chars from email messages
* Files with extension in uppercase are not allowed
* Custom fields do not filter by store

---

## 1.1.69
*(2018-09-28)*

#### Features
* Quick View

---

## 1.1.68
*(2018-09-26)*  

#### Fixed
* Hide inactive users from department list
* Undefined property: stdClass::$host in ...
*  Compatibility with php5 https://bugs.php.net/bug.php?id=66773

---

## 1.1.67
*(2018-09-07)*  

#### Improvements
* Added variable to emails that allows to display labels of "Drop-down list" fields
* Added option "Force store's theme to apply styles"

---

## 1.1.66
*(2018-08-23)*  

#### Fixed
* Error: Script error for: calendar

---

## 1.1.65
*(2018-08-21)*  

#### Fixed
* Contact form submit several times

---

## 1.1.64
*(2018-08-16)*  

#### Fixed
* Compilation error
* Contact form submit several times

---

## 1.1.63
*(2018-07-13)*  

#### Fixed
* Permissions do not applies to admin ticket grid

---

## 1.1.62
*(2018-07-13)*  

#### Fixed
* Permissions do not applies to admin ticket grid
* Issue when customer is logged out on Magento EE after customer updates information from frontend
* Assignee massaction operation

---

## 1.1.61
*(2018-07-04)*  

#### Fixed
* Notice: iconv(): Detected an illegal character in input string
* Centering of search results
* Issue when removed department still uses in settings

---

## 1.1.60
*(2018-05-22)*  

#### Fixed
* Translation
* Duplicated styles

---

## 1.1.59
*(2018-05-08)*  

#### Fixed
* Compatibility with Swissup_AddressFieldManager

#### Improvements
* Added ability to assign order to guest's ticket
* Added ability to set custom text on "Contact Us" page and "Feedback" tab
* Styles for close button. Fixed "Exclude file" option

---

## 1.1.58
*(2018-04-13)*  

#### Fixed
* Compatibility with Swissup_AddressFieldManager

---

## 1.1.57
*(2018-04-03)*  

#### Fixed
* Issue when user can create several custom fields with the same code
* Excluded ticket view page from cache
* Issue when user can create several custom fields with the same code

#### Improvements
* Added 'Third party' condition to 'Last Reply Type' condition

---

## 1.1.56
*(2018-03-28)*  

#### Fixed
* Displaying of custom fields

---

## 1.1.55
*(2018-03-26)*  

#### Fixed
* Displaying of Contact Us popup form when KB is enabled
* Knowledge Base autocomplete submit contact form
* Issue when user assign department to ticket it reset to current user's department

---

## 1.1.54
*(2018-03-16)*  

#### Security
* Solved possible XSS issue in some cases

## 1.1.53
*(2018-03-13)*  

#### Fixed
* Possible error during installation "SQLSTATE[42S22]: Column not found: 1054 Unknown column 'mst_helpdesk_rule.row_id' in 'field list', query was: SELECT MAX(row_id)"
* Unable to close ticket using External Link

---

## 1.1.52
*(2018-03-01)*  

#### Fixed
* Wrong url for ticket view page for multistore

---

## 1.1.51
*(2018-02-21)*  

#### Fixed
* In some cases there is an error during emails fetch Notice: Undefined property: stdClass::$host in ... Imap/Message/Headers.php
* Global ticket search does not work with extended symbols

#### Improvements
* Added ability to hide Help Desk link in customer menu

---

## 1.1.50
*(2018-02-14)*  

#### Improvements
* Added ability to merge tickets

#### Fixed
* On Contact Us page with Smartwave Porto theme contact us form displays incorrectly
* Translation for Working Hours
* Fixed issue with redirect to /helpdesk/ticket/getopen/ in some customized cases
* After migration from Magento 2.0, 2.1 to 2.2+ there is an error in workflow rules 'Unable to unserialize value.'
* Fixed issue with plain text `<br>` in history messages

---

## 1.1.49
*(2018-01-31)*

#### Fixed
* Tickets created via Gateways are using the wrong storeview while sending notification about the new ticket creation
* When cache is enabled feedback tab requires Name an Email for logged in customers
* When customer has turned on autoresponse, emails are going in sending loop
* If ticket's Cc or Bcc fields contain gateway emails emails are going in sending loop
* Order statuses are not translatable on the Create Ticket page

#### Improvements
* Added period filter to reports
* New message event for Issue Watcher & Notification module

---

#### Fixed
* Tickets created via Gateways are using the wrong storeview while sending notification about the new ticket creation
* When cache is enable feedback tab requires Name an Email for logged in customers
* When customer has an autoresponse on emails are going in sending loop
* If ticket's Cc or Bcc fileds contain gateway emails emails are going in sending loop
* Order statuses not translating on ticket create page

#### Improvements
* Added period filter to reports

## 1.1.48
*(2018-01-02)*

#### Fixed
* In Magento 2.2.2, if extension is installed via files upload, helpdesk can't fetch emails and there is error `PHP Fatal error:  Uncaught Error: Class 'Mirasvit_Ddeboer_Imap_Server' not found`

## 1.1.46
*(2017-10-25)*

#### Fixed
* Amount of customer open tickets

---

## 1.1.45
*(2017-10-24)*

#### Fixed
* User signature
* Ticket creation for multistore

---

## 1.1.44
*(2017-10-02)*

#### Fixed
* Small bugs

---

## 1.1.43
*(2017-09-27)*

#### Improvements
* Improved Tickets Grid
* Added Mass Action to allow Mass Change of Ticket Assignee
* Added to Workflow Rules conditions Last Reply Message Type, Customer Email and Customer Name

#### Fixed
* Resolved incorrect email fetch issue
* Compatibility with Magento 2.2.0

---

## 1.1.41
*(2017-09-18)*

#### Improvements
* Added "Change Status" to massactions in backend in ticket grid

#### Fixed
* Solved XSS issue

---

## 1.1.40
*(2017-09-14)*

#### Fixed
* Updating amount of ticket in customer menu

---

## 1.1.39
*(2017-09-05)*

#### Improvements
* Added new condition "Ticket Source (Channel)" to Workflow Rules

#### Fixed
* Compatibility with Magento 2.2.0rc

---

## 1.1.37
*(2017-08-30)*

#### Improvements
* Added ability to block files by extension

---

## 1.1.36
*(2017-08-29)*

#### Fixed
* Variables for email templates

---

## 1.1.35
*(2017-08-08)*

#### Improvements
* Documentation
* UI in backend

---

## 1.1.34
*(2017-07-05)*

#### Fixed
* Notification when another admin user is viewing a ticket

#### Improvements
* Added event "Ticket was converted to RMA"

---

## 1.1.33
*(2017-04-25)*

#### Fixed
* Satisfaction rate urls
* Compatibility of UI components for Magento 2.0.x

---

## 1.1.31
*(2017-03-30)*

#### Fixed
* Compatibility with dashboard widgets
* Ability to delete reserved statuses

#### Features
* Integration with RMA

---

## 1.1.30
*(2017-03-10)*

#### Fixed
* Compatibility with Firefox browser

---

## 1.1.29
*(2017-03-07)*

#### Features
* Created migration scripts from HDMX M1 to HDMX M2

#### Fixed
* Added magento encryptor for gateways passwords

---

## 1.1.28
*(2017-02-24)*

#### Fixed
* Reports issue

---

## 1.1.27
*(2017-02-20)*

#### Improvements
* Added tickets tab to admin customer account page

#### Fixed
* Wrong url for attaches stored in DB

---

## 1.1.26
*(2017-02-08)*

#### Fixed
* Contact form widget

---

## 1.1.25
*(2017-02-06)*
* Add Knowledge base integration to the contact form

## 1.1.24
*(2017-02-03)*

#### Improvements
* Integration with Knowledge base
* Reports

---

## 1.1.23
*(2017-01-31)*

#### Improvements
* Added previous/next button to ticket edit form

#### Fixed
* Option "Show the Help Desk section in the Customer Account"

---

## 1.1.22
*(2017-01-30)*

#### Improvements
* Added ability to assign ticket to order
* Added tickets autosave period

---

## 1.1.21
*(2017-01-23)*

#### Fixed
* Admin ticket grid (affects only 1.1.20)

---

## 1.1.20
*(2017-01-17)*

#### Fixed
* Filters in admin ticket grid

---

## 1.1.19
*(2017-01-13)*

#### Improvements
* Added mass actions for ticket's grid to move tickets to archive or spam
* Added "Use WYSIWYG Editor" option

---

## 1.1.18
*(2017-01-11)*

#### Fixed
* Fixed an issue with sorting in the tickets grid in backend

---

## 1.1.17
*(2017-01-10)*

#### Fixed
* Subject is not displayed in the tickets grid (affects only 1.1.16)

## 1.1.16
*(2016-12-22)*

#### Improvements
* Added email's send date to ticket

---

## 1.1.15
*(2016-12-08)*

#### Fixed
* Issue when email fetch fail due to imap errors

---

## 1.1.14
*(2016-11-29)*

#### Fixed
* Debug messages for cron

---

## 1.1.12
*(2016-10-31)*

#### Fixed
* Issue with Magento bug https://github.com/magento/magento2/issues/5322
* Fixed layout issue (store credit conflict)

#### Improvements
* Added translation files

---

## 1.1.11
*(2016-09-07)*

#### Fixed
* Fixed wrong ticket urls when option "Add Store Code to Urls" enabled

---

## 1.1.9
*(2016-06-29)*

#### Improvements
* Support Magento 2.1.0

#### Fixed
* Fixed translation generation

---

## 1.1.8
*(2016-06-24)*

#### Improvements
* Support Magento 2.1.0

---

## 1.1.7
*(2016-06-22)*

#### Fixed
* Issue with contact form widget

---

## 1.1.6
*(2016-06-10)*

#### Fixed
* Issue with customer satisfaction block in emails
* Exception "Missed phrase" on run i18n:collect-phrases

---

## 1.1.5
*(2016-05-27)*

#### Improvements
* Added store column to admin grids
* Increased default notification check period
* Improved ticket answers' parser

---

## 1.1.4
*(2016-04-30)*

#### Improvements
* Ticket Notifications: added ability to change the check period

#### Fixed
* Fixed cache issue on the contacts page

---

## 1.1.3
*(2016-04-26)*

#### Improvements
* Working schedule. Improved backend  styles

---

## 1.1.2
*(2016-04-21)*

#### Features
* Added working status block in the Customer account > My Tickets
* Working schedule. Added additional columns to backend grid

#### Fixed
* Working schedule status: incorrect time estimation in some cases

#### Improvements
* Improved working schedule status rounding hours

---

## 1.1.1
*(2016-04-20)*

#### Improvements
* Improved css
* Added showing of schedule closed days
* Made schedule titles configurable
* Vertical position of popup

#### Fixed
* Fixed timezones issue

---

## 1.1.0
*(2016-04-11)*

### New Features
* Ability to setup working schedule

#### Improvements
* Improved styles for "Contact Us" form
* Compatibility with 3rd party module Magecomp Recaptcha
* Add validation of patterns before save in backend
* Links in emails
* Improve JQuery load for feedback tab
* Improved styles for Contact Us form
* Improved styles for "Contact Us" form
* Compatibility with 3rd party module Magecomp Recaptcha
* Compatibility with Proto theme
* Add validation of patterns before save in backend
* Links in emails
* Improve JQuery load for feedback tab
* Improved styles for Contact Us form

#### Fixed
* Issue with menu
* Fixed an issue with "Contact Us" button position
* Styles compatibility with Porto theme
* Incorrect sort order in some cases
* Fixed an issue with contant button possiton (for some stores)
* Fixed PHP7 compatibility issue
* Fixed an issue with wrong relation between role tables

---

## 1.0.13
*(2016-03-14)*

#### Improvements
* Compatibility with Proto theme
* Add validation of patterns before save in backend

#### Fixed
* Colors of labels in the frontend
* Emails created by workflow rules dont use html tags correctly
* Missing field in workflow rules

---

## 1.0.12
*(2016-03-07)*

#### Improvements
* Links in emails
* Improve JQuery load for feedback tab

#### Fixed
* Styles compatibility with Porto theme
* Fixed PHP7 compatibility issue
* Fixed an issue with wrong relation between role tables

---

## 1.0.11
*(2016-03-02)*

#### Improvements
* Improved styles for Contact Us form

#### Fixed
* Incorrect sort order in some cases
* Fixed an issue with contact button position (for some stores)

---

## 1.0.10
*(2016-03-02)*

#### Fixed
* Fixed PHP7 compatibility issue

---

## 1.0.9
*(2016-03-01)*

#### Fixed
* Fixed an issue with wrong relation between role tables

#### Improvements
* Improved popup position on mobile devices

---

## 1.0.8
*(2016-02-25)*

#### Improvements
* Add email preview preheaders
* Improve history of ticket in backend
* Ability to include attachments in mails

#### Fixed
* In some cases fatal error on all pages of store
* Fixed an issue with wrong priority at ticket view page
* Remove attached files when we remove a ticket

---

## 1.0.7
*(2016-02-15)*

#### Improvements
* Improve history of ticket in backend
* Ability to include attachments in mails
* Improved emails styles and layout

#### Fixed
* In some cases fatal error on all pages of store
* Fixed an issue with wrong priority at ticket view page
* Fixed an issue with loggin cron job errors (on fetch)
* Fixed an issue with wrong location of lib folder
* Fixed an issue with IMAP extension validation when save gateway
* Fixed an issue with workflow rules
* Fixed an issue with change Save
* Removed field is_internal
* HDMX2-24 - field order is not required in the customer account
* HDMX2-26 - Fixed error Invalid template file: 'page/js/calendar.phtml'
* Fixed an issue with switching label colors on priority/status edit/grid pages
* Fixed style issue with gateway password field
* Adjusted css styles

---
