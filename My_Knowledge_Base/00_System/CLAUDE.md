# CLAUDE.md

Behavioral guidelines to reduce common LLM coding mistakes. Merge with project-specific instructions as needed.

**Tradeoff:** These guidelines bias toward caution over speed. For trivial tasks, use judgment.

## 1. Think Before Coding

**Don't assume. Don't hide confusion. Surface tradeoffs.**

Before implementing:
- State your assumptions explicitly. If uncertain, ask.
- If multiple interpretations exist, present them - don't pick silently.
- If a simpler approach exists, say so. Push back when warranted.
- If something is unclear, stop. Name what's confusing. Ask.

## 2. Simplicity First

**Minimum code that solves the problem. Nothing speculative.**

- No features beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "configurability" that wasn't requested.
- No error handling for impossible scenarios.
- If you write 200 lines and it could be 50, rewrite it.

Ask yourself: "Would a senior engineer say this is overcomplicated?" If yes, simplify.

## 3. Surgical Changes

**Touch only what you must. Clean up only your own mess.**

When editing existing code:
- Don't "improve" adjacent code, comments, or formatting.
- Don't refactor things that aren't broken.
- Match existing style, even if you'd do it differently.
- If you notice unrelated dead code, mention it - don't delete it.

When your changes create orphans:
- Remove imports/variables/functions that YOUR changes made unused.
- Don't remove pre-existing dead code unless asked.

The test: Every changed line should trace directly to the user's request.

## 4. Goal-Driven Execution

**Define success criteria. Loop until verified.**

Transform tasks into verifiable goals:
- "Add validation" → "Write tests for invalid inputs, then make them pass"
- "Fix the bug" → "Write a test that reproduces it, then make it pass"
- "Refactor X" → "Ensure tests pass before and after"

For multi-step tasks, state a brief plan:
```
1. [Step] → verify: [check]
2. [Step] → verify: [check]
3. [Step] → verify: [check]
```

Strong success criteria let you loop independently. Weak criteria ("make it work") require constant clarification.

---

**These guidelines are working if:** fewer unnecessary changes in diffs, fewer rewrites due to overcomplication, and clarifying questions come before implementation rather than after mistakes.


# [[CLAUDE.md]]

## 📌 Brief Summary

CLAUDE.md는 대규모 언어 모델(LLM)이 코딩 과정에서 흔히 저지르는 실수를 줄이기 위한 행동 지침 파일입니다. 속도보다 신중함을 우선시하며, 프로젝트별 지침과 결합하여 코드의 복잡성을 낮추고 정확도를 높이는 것을 목표로 합니다.

## 📖 Core Content

CLAUDE.md는 효율적인 협업과 코드 품질 유지를 위해 다음과 같은 4가지 핵심 원칙을 제시합니다.

- **코딩 전 사고 (Think Before Coding)**: 가정을 명확히 기술하고, 불확실한 부분은 질문하며, 여러 해석이 가능할 경우 독단적으로 선택하지 않고 대안을 제시해야 합니다.
    
- **단순성 우선 (Simplicity First)**: 요청받지 않은 기능을 추가하거나 불필요한 추상화를 지양하며, 최소한의 코드로 문제를 해결해야 합니다.
    
- **정밀한 수정 (Surgical Changes)**: 요청과 직접 관련된 부분만 수정하며, 기존 스타일을 존중하고 본인의 작업으로 인해 발생한 불필요한 코드(imports 등)만 정리합니다.
    
- **목표 중심 실행 (Goal-Driven Execution)**: 성공 기준을 명확히 정의하고, 다단계 작업의 경우 각 단계마다 검증 과정을 포함한 계획을 세워 실행합니다.
    

## 🔗 Knowledge Connections

- **Related Topics:** [[LLM 코딩 가이드라인]], [[클린 코드 (Clean Code)]], [[테스트 주도 개발 (TDD)]]
    
- **Projects/Contexts:** [[AI 협업 워크플로우]], [[코드 리뷰 표준]]
    
- **Contradictions/Notes:** 이 가이드라인은 속도보다 정확성과 신중함에 편향되어 있으므로, 사소한 작업에서는 사용자의 판단에 따라 유연하게 적용할 수 있습니다.
    

---

_Last updated: 2026-04-30_