# laravelProject-sanctum-vue
Replaces old laravel browser game project. Uses vue.js, token auth with all components properly 
seperated into templates with their own script.    

Uses vue.js for front end components and routing

Uses laravel for back end work with mySQL, api routes and authentication with laravel sanctum  

Rpg Game  
Game code was all client side in one .js file, conversion in progress to server side  
character creation screen, response  
https://user-images.githubusercontent.com/16962503/104146260-f79a4780-537e-11eb-9dd3-73b8e92bdabc.png  

Game chat board    
Working on adding more functionality over the old textboard  
Posts listed on load  
Replies can be made for each post  
New posts can be made on form at bottom  
All posts fetched on making post  
Replies fetched on making reply under post. Closes reply form and presses expand button under post, showing
all replies to that post  
Replies fetched on clicking expand button under post, and listed under post in alternating colour  
https://user-images.githubusercontent.com/16962503/104082809-e968f180-51ed-11eb-8baa-8590d77b89bb.png

Game Cash Store  
Lists all cash shop items available on load  
Store inventory populated with database seeder that gets data from a provided JSON file  
Item descriptions can be expanded  
Cart listing and store inventory toggle button on bottom 
Cart contents modifiable  
Will send purchase detail to email  
Shop listing:     
https://user-images.githubusercontent.com/16962503/104404602-dddc3a00-550f-11eb-9211-d6c4f8d3e9a2.png  
Cart listing:  
https://user-images.githubusercontent.com/16962503/105121343-ce696d80-5a88-11eb-997d-ff317d63a16a.png
