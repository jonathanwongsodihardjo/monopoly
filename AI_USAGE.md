# AI Usage
- generating the code for reading and parsing the shell arguments
- asking the opinion of my design choices
- asking to optimise if a player has rolled a number that will allow them to pass go and AI responded with this approach: (position + roll) mod total space inside board instead of doing (position + roll) - total space inside board
- asking it to generate automated testing for trivial classes

# Where AI Went Wrong
- Initially when I asked the AI for its opinion, it actually suggested me to use interface instead of abstract class for `Space`
- The AI didn't suggest that I should BCMath package for calculating money received / paid for each player as it didn't understand that I was dealing with money and not just integers.