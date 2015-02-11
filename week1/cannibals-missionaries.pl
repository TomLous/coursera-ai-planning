initial_state(mc, mc(left, bank(3,3), bank(0,0))).
final_state(mc(right, bank(0,0), bank(3,3))).
 
move(mc(left, L, _R), Boat) :-
    choose_passengers(L, Boat).
move(mc(right, _L, R), Boat) :-
    choose_passengers(R, Boat).
 
choose_passengers(bank(C, _M), boat(2, 0)) :- 
    C > 1.
choose_passengers(bank(_C, M), boat(0, 2)) :- 
    M > 1.
choose_passengers(bank(C, M), boat(1, 1))  :- 
    C > 0, M  > 0.
choose_passengers(bank(C, _M), boat(1, 0)) :- 
    C > 0.
choose_passengers(bank(_C, M), boat(0, 1)) :- 
    M > 0.
 
update(mc(B, L, R), Boat, mc(B1, L1, R1)) :-
    update_boat(B, B1),
    update_banks(Boat, B, L, R, L1, R1).
 
update_boat(left, right).
update_boat(right, left).
 
update_banks(alone, _B, L, R, L, R).
update_banks(boat(C, M), left, 
             bank(LC, LM), bank(RC, RM), 
             bank(LC1, LM1), bank(RC1, RM1)) :-
    LM1 is LM - M,
    RM1 is RM + M,
    LC1 is LC - C,
    RC1 is RC + C.
update_banks(boat(C, M), right,
             bank(LC, LM), bank(RC, RM), 
             bank(LC1, LM1), bank(RC1, RM1)) :-
    LM1 is LM + M,
    RM1 is RM - M,
    LC1 is LC + C,
    RC1 is RC - C.
 
legal(mc(left, _L, R)) :-
    \+ illegal(R).
 
legal(mc(right, L, _R)) :-
    \+ illegal(L).
 
illegal(bank(C, M)) :-
    M > C.
 
solve_dfs(State, _History, [step(State)]) :-
    final_state(State).
solve_dfs(State, History, [step(State, Move)|States]) :-
    move(State, Move),
    update(State, Move, State1),
    legal(State1),
    \+ member(State1, History),
    solve_dfs(State1, [State1|History], States).
 
print_step(step(mc(left, L, R), B)) :-
    format("~p *                     ~p~n", [L, R]),
    format("             -> ~p ->          ~n", [B]).
print_step(step(mc(right, L, R), B)) :-
    format("~p                     * ~p~n", [L, R]),
    format("             <- ~p <-          ~n", [B]).
print_step(step(mc(left, L, R))) :-
    format("~p *                     ~p~n", [L, R]).
print_step(step(mc(right, L, R))) :-
    format("~p                     * ~p~n", [L, R]).
 
print_steps([A|As]) :-
    print_step(A),
    print_steps(As).
print_steps([]).
 
main :-
    initial_state(mc, State),
    solve_dfs(State, [State], Steps),
    print_steps(Steps).